# See here for image contents: https://github.com/microsoft/vscode-dev-containers/tree/v0.195.0/containers/php/.devcontainer/base.Dockerfile
# [Choice] PHP version (use -bullseye variants on local arm64/Apple Silicon): 8, 8.0, 7, 7.4, 7.3, 8-bullseye, 8.0-bullseye, 7-bullseye, 7.4-bullseye, 7.3-bullseye, 8-buster, 8.0-buster, 7-buster, 7.4-buster, 7.3-buster
ARG VARIANT=8-bullseye
FROM mcr.microsoft.com/devcontainers/php:0-${VARIANT}

# [Choice] Node.js version: none, lts/*, 16, 14, 12, 10
ARG NODE_VERSION="none"
RUN if [ "${NODE_VERSION}" != "none" ]; then su vscode -c "umask 0002 && . /usr/local/share/nvm/nvm.sh && nvm install ${NODE_VERSION} 2>&1"; fi

# [Optional] Uncomment this section to install additional OS packages.
RUN apt-get update && export DEBIAN_FRONTEND=noninteractive \
&& apt-get -y install --no-install-recommends gnome-terminal vim iptables


# RUN sudo apt install -y apt-transport-https ca-certificates curl software-properties-common
# RUN curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo apt-key add -
# RUN sudo add-apt-repository 'deb [arch=amd64] https://download.docker.com/linux/ubuntu focal stable'

# RUN apt-cache policy docker-ce
# RUN sudo apt install -y docker-ce


# [Optional] Uncomment this line to install global node packages.
# RUN su vscode -c "source /usr/local/share/nvm/nvm.sh && npm install -g <your-package-here>" 2>&1

# Install Docker and Lando.
RUN curl -fsSL https://get.docker.com -o get-docker.sh
RUN sh get-docker.sh
RUN wget https://files.lando.dev/installer/lando-x64-stable.deb
RUN sudo dpkg -i --ignore-depends=docker-ce lando-x64-stable.deb
RUN lando version
RUN rm lando-x64-stable.deb
CMD [“echo”,”Image created”]

# Install DDEV.
RUN curl https://apt.fury.io/drud/gpg.key | sudo apt-key add -
RUN echo "deb https://apt.fury.io/drud/ * *" | sudo tee -a /etc/apt/sources.list.d/ddev.list
RUN sudo apt update && sudo apt install -y ddev
