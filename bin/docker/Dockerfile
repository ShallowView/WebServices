FROM caddy:2.10.0-builder-alpine AS builder

RUN xcaddy build \
    --with github.com/caddyserver/jsonc-adapter

FROM node:24.0.2-alpine3.21 AS node-base

ENV SOURCES_ROOT=/usr/src/webservices

COPY ./ $SOURCES_ROOT
WORKDIR $SOURCES_ROOT

# Frontend
FROM node-base AS frontend

RUN npm install --verbose
RUN npm run build -w frontend

# Doc
FROM node-base AS doc

RUN sh bin/compileDocs.sh \
	backend/resource/specification/openapi-shallowview.yaml \
	backend/resource/public/doc

# Final
FROM caddy:2.10.0-alpine

ARG DOCKER_FILES_ROOT=bin/docker

ENV CYUSER=caddy
ENV CYCONFIG=/etc/caddy
ENV CYROOT=/usr/share/caddy

COPY --from=builder /usr/bin/caddy /usr/bin/caddy

COPY $DOCKER_FILES_ROOT/Caddyfile.jsonc $CYCONFIG

# Copies service files.
RUN rm ${CYROOT}/index.html
RUN mkdir -p $CYROOT/master $CYROOT/api $CYROOT/doc

COPY --from=frontend /usr/src/webservices/frontend/build $CYROOT/master

COPY backend/lib/ $CYROOT/api/lib/
COPY backend/resource/ $CYROOT/api/resource/
COPY backend/src/ $CYROOT/api/src/

COPY --from=doc /usr/src/webservices/out/doc/ $CYROOT/doc/

# Unpriviliged user
RUN addgroup -S $CYUSER
RUN adduser -S $CYUSER -G $CYUSER -h $CYROOT

RUN chown -R $CYUSER:$CYUSER $XDG_CONFIG_HOME $XDG_DATA_HOME 

USER $CYUSER

CMD ["sh","-c",\
    "caddy run --config $CYCONFIG/Caddyfile.jsonc --adapter jsonc"\
]