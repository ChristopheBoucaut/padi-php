# Table des matières

- [Table des matières](#table-des-matières)
- [Command](#command)
  - [Objectif](#objectif)
  - [Utilisation](#utilisation)
- [DomainEvent](#domainevent)
  - [Objectif](#objectif-1)
  - [Utilisation](#utilisation-1)
- [Outcome](#outcome)
  - [Objectif](#objectif-2)
  - [Utilisation](#utilisation-2)
- [Specification](#specification)
  - [Objectif](#objectif-3)
  - [Utilisation](#utilisation-3)

# [Command](/src/Command/)

## Objectif

Il s'agit d'une implémentation minimale permettant de suivre le [design pattern command](https://en.wikipedia.org/wiki/Command_pattern). On y retrouvera les interfaces minimales et les implémentations les plus courantes.

> [!IMPORTANT] En résumé
> L'idée globale consiste à éviter d'effectuer une action en direct mais plutôt de retourner toutes les informations nécessaires pour décrire cette action. On peut ainsi la différer à un autre service, l'afficher au lieu de l'exécuter, l'historiser, la faire tourner dans une transaction SQL, etc.
>
> **La différence avec un événement que l'on dispatch, c'est qu'une commande ne doit être traitée que par un seul handler.**

## Utilisation

1. Créer une nouvelle *Command*
    ```php
    <?php

    declare(strict_types = 1);

    namespace My\Domain;

    use Padi\Command\CommandInterface;

    readonly class MyCommand implements CommandInterface
    {
        public function __construct(
            public string $data = "fake",
        ) {
        }
    }
    ```
2. On peut ensuite manipuler l'objet *Command* ou la dispatcher pour qu'elle soit exécutée
    > [!TIP] Astuce
    > Vous pouvez implémenter votre propre bus avec l'interface `CommandBusInterface` ou utiliser un des bus déjà présents dans ce projet.
    ```php
    // Sequential bus - Execute command after register handler by command class
    $bus = new SequentialCommandBus();
    $bus->register(MyCommand::class, static function (MyCommand $cmd) use (&$value1): void {
        $value1 = $cmd->data;
    });
    $bus->dispatch(new MyCommand());

    // Collection bus - Just collect commands - no real execution (useful for tests or displaying information)
    $bus = new CollectingCommandBus();
    $bus->dispatch(new MyCommand());
    $bus->dispatched; // return commands
    ```

# [DomainEvent](/src/DomainEvent/)

## Objectif

Dans un projet de clean architecture, le *Domain* peut générer des événements qu'il faut ensuite dispatcher au niveau du *UseCase*. Le périmètre ici se limite à la collecte de ces événements au sein du *Domain* et à leur dispatch depuis la couche *UseCase*.

> [!NOTE] Aller plus loin
> L'idée avait été d'implémenter le dispatch au niveau des repositories de façon automatique lors du save/delete afin d'automatiser au maximum le dispatch. Cela a été abandonné car des événements pourraient être générés dans d'autres circonstances qu'un save/delete, et on aurait donc dans tous les cas des logiques différentes.

## Utilisation

1. Implémenter l'interface `DomainEventRecorderInterface` dans votre *Entity* du *Domain*.
    ```php
    <?php

    declare(strict_types = 1);

    namespace My\Domain;

    use Padi\DomainEvent\DomainEventRecorderInterface;
    use Padi\DomainEvent\DomainEventRecorderTrait;

    class MyEntity implements DomainEventRecorderInterface
    {
        use DomainEventRecorderTrait;

        public function doSomething(): void
        {
            $this->recordDomainEvent((object) ['key1' => 'data1', 'key2' => 'data2']);
        }
    }
    ```
    > [!TIP] Astuce
    > Le trait `DomainEventRecorderTrait` est là pour remplir le besoin de base de l'interface.
2. Dans la partie *UseCase*, vous pouvez récupérer les événements générés et les dispatcher manuellement ou simplement passer par `DomainEventDispatcher` qui s'occupera de dépiler les événements d'une ou plusieurs entités.
    ```php
    <?php

    $dispatcher = new DomainEventDispatcher($yourEventDispatcher);
    $dispatcher->dispatch($entity1, $entity2);
    ```

# [Outcome](/src/Outcome/)

## Objectif

Dans un projet de clean architecture, un *UseCase* va retourner un objet *Response* qui lui est propre. Il est courant que cet objet porte à la fois la logique de réussite et d'échec. On se retrouve donc avec des propriétés pouvant être *nullable* car il n'y a rien à fournir dans les cas où la réponse représente une erreur. Cela complexifie le code lors de la manipulation de la réponse.

Pour garder un code plus structuré, l'idée du scope *Outcome* est d'exposer des interfaces et classes permettant de s'approcher de la logique du langage Rust, où les fonctions peuvent retourner un objet `Ok` en cas de réussite — qui encapsule la vraie réponse — ou un objet `Err` qui encapsule l'erreur si l'exécution rencontre un problème.

> [!IMPORTANT] Le choix de la simplicité
> Dans cette implémentation, on est resté volontairement léger car PHP ne propose pas la même simplicité que Rust pour tester la réussite et dépiler en même temps l'objet contenu dans `Ok` ou `Err`.

## Utilisation

1. Créer la classe *Response* qui représente **uniquement** la réussite :
    ```php
    <?php

    declare(strict_types = 1);

    namespace My\UseCase;

    use Padi\Outcome\OkInterface;

    readonly class MyResponse implements OkInterface
    {
        public function __construct(
            public string $data,
        ) {
        }
    }
    ```
2. Créer la ou les classe(s) représentant les erreurs possibles :
    ```php
    <?php

    declare(strict_types = 1);

    namespace My\UseCase;

    use Padi\Outcome\ErrInterface;

    class MyErr implements ErrInterface
    {
        #[\Override]
        public function getMessage(): string
        {
            return "This is an error in my use case";
        }

        #[\Override]
        public function getContext(): array
        {
            return ['more', 'data', 'for logs ?'];
        }

        #[\Override]
        public function __toString(): string
        {
            return $this->getMessage();
        }
    }
    ```
3. Créer son *UseCase* :
    ```php
    <?php

    declare(strict_types = 1);

    namespace My\UseCase;

    class MyUseCase
    {
        // You can add more Err in return types and you can use an interface to "group" errors for same UseCase.
        public function execute(MyUseCaseRequest $request): MyResponse|MyErr
        {
            if ($request->price < 0) {
                return new MyErr();
            }

            return new MyResponse();
        }
    }
    ```
4. Manipuler la réponse de son *UseCase* :
    ```php
    <?php

    declare(strict_types = 1);

    use Padi\Outcome\ErrInterface;

    $response = new MyUseCase()->execute(new MyUseCaseRequest(price: 5));
    if ($response instanceof ErrInterface) {
        // Here, we simply throw an exception for all errors, but you can handle each error class with more precision.
        $logger->warning($response->getMessage(), $response->getContext());
        throw new \Exception($response->getMessage());
    }
    $response->data; // it's always valid, never nullable.
    ```

# [Specification](/src/Specification/)

## Objectif

Il s'agit d'une implémentation minimale permettant de suivre le [design pattern specification](https://en.wikipedia.org/wiki/Specification_pattern).

> [!IMPORTANT] En résumé
> L'idée globale consiste à transformer des multitudes de `if` en classes simples, combinables et aux noms explicites, afin de centraliser les règles métiers et de gagner en lisibilité.

## Utilisation

1. Créer une nouvelle *Specification* en héritant de `AbstractSpecification` tout en précisant la valeur du générique (dans l'exemple un objet `Money`) :
    ```php
    <?php

    declare(strict_types = 1);

    namespace My\Domain;

    use Padi\Specification\AbstractSpecification;
    use Foo\Money;

    /** @extends AbstractSpecification<Money> */
    class PriceIsPositive extends AbstractSpecification
    {
        #[\Override]
        protected function doIsSatisfiedBy(mixed $money): bool
        {
            return $money->value >= 0;
        }

        #[\Override]
        protected function getError(): string
        {
            return '[OPTIONNAL] Only if you need to specifiy error when test\'s test is false';
        }
    }

    $specification = new PriceIsPositive();
    $specification->isSatisfiedBy($money); // return true|false
    $specification->getLastErrors(); // return error msgs
    ```
2. Vous pouvez ensuite utiliser `AndSpecification` et/ou `OrSpecification` pour combiner les règles métiers.
    ```php
    <?php

    declare(strict_types = 1);

    namespace My\Domain;

    use Padi\Specification\AndSpecification;
    use Padi\Specification\OrSpecification;

    $specification = new AndSpecification(
        new OrSpecification(
            $specification1,
            $specification2,
        ),
        new AndSpecification(
            new OrSpecification(
                $specification3,
                $specification4,
            ),
            $specification5,
        ),
    );
    $specification->isSatisfiedBy($money); // return true|false
    $specification->getLastErrors(); // return error msgs
    ```

    > [!WARNING] Limitation actuelle
    > Il n'est pas possible de combiner des spécifications qui ne gèrent pas un même type. À terme, un "transformer" permettant de convertir les valeurs entre les différentes spécifications pourrait être mis en place.
