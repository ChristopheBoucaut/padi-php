# Table of Contents

- [Table of Contents](#table-of-contents)
- [Command](#command)
  - [Purpose](#purpose)
  - [Usage](#usage)
- [DomainEvent](#domainevent)
  - [Purpose](#purpose-1)
  - [Usage](#usage-1)
- [Outcome](#outcome)
  - [Purpose](#purpose-2)
  - [Usage](#usage-2)
- [Specification](#specification)
  - [Purpose](#purpose-3)
  - [Usage](#usage-3)

# [Command](/src/Command/)

## Purpose

This is a minimal implementation following the [command design pattern](https://en.wikipedia.org/wiki/Command_pattern). It provides the minimal interfaces and the most common implementations.

> [!IMPORTANT] In a nutshell
> The overall idea is to avoid performing an action directly, and instead return all the information needed to describe that action. It can then be deferred to another service, displayed rather than executed, stored in a history log, run inside a SQL transaction, etc.
>
> **The difference from a dispatched event is that a command must be handled by one and only one handler.**

## Usage

1. Create a new *Command*
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
2. You can then manipulate the *Command* object or dispatch it to be executed
    > [!TIP] Tip
    > You can implement your own bus using the `CommandBusInterface` interface, or use one of the buses already available in this project.
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

## Purpose

In a clean architecture project, the *Domain* can generate events that must then be dispatched at the *UseCase* layer. The scope here is limited to collecting these events within the *Domain* and dispatching them from the *UseCase* layer.

> [!NOTE] Going further
> The idea had been to implement automatic dispatching at the repository level during save/delete operations to automate the process as much as possible. This was abandoned because events could be generated in circumstances other than a save/delete, which would lead to different dispatch logic in all cases anyway.

## Usage

1. Implement the `DomainEventRecorderInterface` interface in your *Domain* *Entity*.
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
    > [!TIP] Tip
    > The `DomainEventRecorderTrait` trait covers the basic requirements of the interface.
2. In the *UseCase* layer, you can retrieve the generated events and dispatch them manually, or simply use `DomainEventDispatcher`, which will take care of processing the events from one or more entities.
    ```php
    <?php

    $dispatcher = new DomainEventDispatcher($yourEventDispatcher);
    $dispatcher->dispatch($entity1, $entity2);
    ```

# [Outcome](/src/Outcome/)

## Purpose

In a clean architecture project, a *UseCase* returns a *Response* object specific to that use case. It is common for this object to carry both success and failure logic at the same time. This results in properties that may be *nullable* because there is nothing to provide when the response represents an error. This complicates the code when manipulating the response.

To keep the code more structured, the *Outcome* scope exposes interfaces and classes that allow getting closer to the logic of the Rust language, where functions can return an `Ok` object on success — encapsulating the actual response — or an `Err` object encapsulating the error if execution encounters a problem.

> [!IMPORTANT] Keeping it simple
> In this implementation, we deliberately kept things lightweight because PHP does not offer the same simplicity as Rust for testing success and unwrapping the value contained in `Ok` or `Err` at the same time.

## Usage

1. Create the *Response* class that represents **only** success:
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
2. Create the class(es) representing the possible errors:
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
3. Create the *UseCase*:
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
4. Handle the *UseCase* response:
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

## Purpose

This is a minimal implementation following the [specification design pattern](https://en.wikipedia.org/wiki/Specification_pattern).

> [!IMPORTANT] In a nutshell
> The overall idea is to turn countless `if` statements into simple, composable classes with meaningful names, in order to centralize business rules and improve readability.

## Usage

1. Create a new *Specification* by extending `AbstractSpecification` while specifying the generic type (in this example a `Money` object):
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
2. You can then use `AndSpecification` and/or `OrSpecification` to combine business rules.
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

    > [!WARNING] Current limitation
    > It is not possible to combine specifications that do not handle the same type. A "transformer" allowing values to be converted between different specifications may be introduced in the future.
