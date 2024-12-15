# Running Containers for the Project

This document provides a step-by-step guide on how to install Docker, Docker Compose, run containers, and configure the project.

---

## Installing Docker

1. Download and execute the script to install Docker:

    ```bash
    curl -fsSL https://get.docker.com -o get-docker.sh
    sudo sh get-docker.sh
    ```

2. Verify that Docker is installed correctly:

    ```bash
    docker --version
    ```

3. Add the current user to the Docker group to run commands without `sudo`:

    ```bash
    sudo usermod -aG docker $USER
    ```

    After this, log out and log back in.

---

## Installing Docker Compose

1. Install the Docker Compose plugin:

    ```bash
    sudo apt-get update
    sudo apt-get install docker-compose-plugin
    ```

2. Verify Docker Compose installation:

    ```bash
    docker compose version
    ```

---

## Project Configuration

1. Create a `docker-compose.yml` file in the project root with the following content:

    ```yaml
    version: "3.8"
    
    services:
      nginx:
        image: dotskinmaksim/nginx:latest
        container_name: nginx-container
        ports:
          - "80:8080"
        restart: unless-stopped
        dns:
          - 172.18.0.3
        networks:
          custom_network:
            ipv4_address: 172.18.0.2
    
      mysql:
        image: dotskinmaksim/mysql:latest
        container_name: mysql-container
        ports:
          - "3306:3306"
        environment:
          MYSQL_ROOT_PASSWORD: 123
          MYSQL_DATABASE: ads_database
        restart: unless-stopped
        networks:
          custom_network:
            ipv4_address: 172.18.0.4
      
      dns:
        image: dotskinmaksim/dns:latest
        container_name: dns-container
        ports:
          - "5353:53/udp"
          - "5353:53/tcp"
        restart: unless-stopped
        networks:
          custom_network:
            ipv4_address: 172.18.0.3
    
    networks:
      custom_network:
        driver: bridge
        ipam:
          config:
            - subnet: 172.18.0.0/24
    ```

2. Set the IP address of your virtual machine:

    ```bash
    172.16.13.1
    ```

---

## Building and Running Containers

1. Start the containers using Docker Compose:

    ```bash
    docker compose up -d
    ```

2. Check running containers:

    ```bash
    docker ps
    ```

3. Set the DNS server:

    ```bash
    172.18.0.3
    ```

4. The website will be available at:

    ```bash
    ls2.myads.loc
    ```

---

## Stopping Containers

To stop the containers, run:

```bash
docker compose down
```

---

## Troubleshooting

1.	If you encounter a network error, ensure your network is correctly configured in the networks section of the docker-compose.yml file.
2.	Check container logs for diagnostics:

    ```bash
    docker logs <container_name>
    ```

---

Now your project is ready to run on Docker. Happy coding!
