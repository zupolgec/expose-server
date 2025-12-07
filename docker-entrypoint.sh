#!/bin/bash

sed -i "s|'username' => 'secret'|'${username}' => '${password}'|g" ${exposeConfigPath}

if [[ $# -eq 0 ]]; then
    exec /src/expose-server serve ${domain} --port ${port} --validateAuthTokens
else
    exec /src/expose-server "$@"
fi
