FROM php:8.5-cli

WORKDIR /app

# Add composer
COPY --from=composer/composer /usr/bin/composer /usr/bin/composer

# Configure user in container to use host user
ARG UID=1000
ARG GID=1000
RUN groupadd -g $GID appuser && useradd -u $UID -g appuser -m appuser
USER appuser

CMD ["php", "-a"]
