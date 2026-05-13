# vk-2026

## Docker CLI - Kompletný zoznam príkazov s vysvetlením

### 1. Vytvorenie Docker image

Vytvorenie Docker image s názvom `vk-apache` na základe Dockerfile uloženého ako `config/Dockerfile-apache`.  
Bodka na konci znamená, že sa build vykonáva v aktuálnom priečinku (kontext).

```bash
docker build -t vk-apache -f config/Dockerfile-apache .
```

---

### 2. Spustenie kontajnera

Spustenie kontajnera z image `vk-apache`, kde je vnútorný port kontajnera `80` presmerovaný na port `8080` hostiteľského počítača.  
Parameter `-d` znamená "detached" mód (kontajner beží na pozadí).

```bash
docker run -d -p 8080:80 vk-apache
```

---

### 3. Interaktívne spustenie kontajnera

Spustenie kontajnera s pripojením do interaktívnej konzoly (`/bin/bash`) bez vykonania príkazu CMD alebo ENTRYPOINT (napr. apache2-foreground).  
Parametre `-it` znamenajú interaktívny mód s terminálom.

```bash
docker run -it -p 8080:80 vk-apache /bin/bash
```

---

### 4. Prístup do bežiaceho kontajnera

Pripojenie sa do konzoly existujúceho bežiaceho kontajnera (napríklad `trusting_shamir`).

```bash
docker exec -it trusting_shamir /bin/bash
```

---

### 5. Zobrazenie zoznamu kontajnerov

Zobrazenie všetkých bežiacich kontajnerov.

```bash
docker ps
```

Zobrazenie všetkých kontajnerov, aj tých, ktoré sú zastavené.

```bash
docker ps -a
```

---

### 6. Zastavenie a vymazanie kontajnera

Zastavenie bežiaceho kontajnera s názvom `trusting_shamir`.

```bash
docker stop trusting_shamir
```

Vymazanie kontajnera s názvom `trusting_shamir`.

```bash
docker rm trusting_shamir
```

---

### 7. Zobrazenie dostupných Docker images

Zobrazenie všetkých lokálnych images uložených na disku.

```bash
docker images
```

---

### 8. Práca s volume (perzistentné dáta)

Vytvorenie volume s názvom `mydata`.

```bash
docker volume create mydata
```

Spustenie kontajnera s pripojenou volume `mydata`, ktorá bude namapovaná do priečinka `/var/www/html/data` v kontajneri.

```bash
docker run -d -p 8080:80 -v mydata:/var/www/html/data vk-apache
```

Zobrazenie zoznamu všetkých volume.

```bash
docker volume ls
```

Detailné informácie o konkrétnej volume `mydata`.

```bash
docker volume inspect mydata
```

Vymazanie volume `mydata`.

```bash
docker volume rm mydata
```

---

### 9. Práca s logmi kontajnera

Zobrazenie všetkých logov kontajnera `trusting_shamir`.

```bash
docker logs trusting_shamir
```

Sledovanie logov kontajnera `trusting_shamir` v reálnom čase (priebežne).

```bash
docker logs -f trusting_shamir
```

---

### 10. Práca so sieťami

Vytvorenie izolovanej siete `mynetwork`.

```bash
docker network create mynetwork
```

Spustenie kontajnera `web`, ktorý bude pripojený k sieti `mynetwork`.

```bash
docker run -d --network=mynetwork --name web vk-apache
```

Zobrazenie zoznamu všetkých sietí.

```bash
docker network ls
```

Detailné informácie o konkrétnej sieti `mynetwork`.

```bash
docker network inspect mynetwork
```

Vymazanie siete `mynetwork`.

```bash
docker network rm mynetwork
```

---

### 11. Export a import Docker image

Export image `vk-apache` do súboru `vk-apache.tar`.

```bash
docker save vk-apache > vk-apache.tar
```

Import image zo súboru `vk-apache.tar`.

```bash
docker load < vk-apache.tar
```

---

## Záver Docker CLI

Tento zoznam zahŕňa základné aj pokročilejšie príkazy, ktoré potrebujeme pri práci s Dockerom — od vytvárania image a kontajnerov, až po správu volume, sietí a logov.

## Docker Compose - Kompletný zoznam príkazov s vysvetlením

---

### 1. Spustenie Docker Compose projektu

Spustenie všetkých kontajnerov definovaných v súbore `docker-compose.yml`. Príkaz automaticky vytvorí potrebné siete, volumes a spustí kontajnery v popredí.

```bash
docker compose up
```

Pre spustenie kontajnerov na pozadí (detached mód) použijeme parameter `-d`.

```bash
docker compose up -d
```

---

### 2. Zastavenie Docker Compose projektu

Zastaví všetky bežiace kontajnery definované v aktuálnom Docker Compose projekte, pričom ponechá zachované siete, volumes a vytvorené kontajnery.

```bash
docker compose stop
```

---

### 3. Ukončenie Docker Compose projektu

Zastaví a odstráni kontajnery, siete a ďalšie zdroje vytvorené príkazom `docker compose up`. Štandardne ponechá volumes zachované.

```bash
docker compose down
```

Pre odstránenie volumes použijeme parameter `-v`.

```bash
docker compose down -v
```

---

### 4. Build Docker Compose image

Vytvorí alebo obnoví images definované v `docker-compose.yml`, ale nespustí kontajnery.

```bash
docker compose build
```

Pre build bez použitia cache (kompletný rebuild) použijeme parameter `--no-cache`.

```bash
docker compose build --no-cache
```

---

## Záver Docker Compose

Tento rozšírený zoznam pokrýva dôležité príkazy Docker Compose, ktoré zjednodušujú prácu s viacerými kontajnermi naraz pomocou jedného konfiguračného súboru.

## Kubernetes CLI - Kompletný zoznam príkazov s vysvetlením

### 1. Zoznam namespaces v clustri
Zobrazenie všetkých namespaces dostupných v Kubernetes clustri.

```bash
kubectl get namespaces
```

---

### 2. Vytvorenie nového namespace
Vytvorenie vlastného namespace s názvom `ukf`.

```bash
kubectl create namespace ukf
```

---

### 3. Aplikovanie konfigurácie zo súboru
Univerzálny príkaz na nahratie a spustenie všetkého, čo je definované v YAML súbore (Deployment, Service, ConfigMap, atď.).

```bash
kubectl apply -f deployment.yaml
```
### 3.1. Aplikovanie konfigurácie celého folderu

```bash
kubectl apply -f config/k8s-exam/
```

---

### 4. Zoznam Deploymentov v namespace `ukf`
Zobrazenie všetkých deploymentov v určenom namespace.

```bash
kubectl -n ukf get deployments
```

---

### 5. Zoznam Services v namespace `ukf`
Zobrazenie všetkých Kubernetes Services (napr. ClusterIP, NodePort) v namespace.

```bash
kubectl -n ukf get svc
```

---

### 6. Zoznam ConfigMap v namespace `ukf`
Zobrazenie všetkých ConfigMap objektov v namespace `ukf`.

```bash
kubectl -n ukf get configmap
```

---

### 7. Zoznam Podov v namespace `ukf`
Zobrazenie všetkých bežiacich podov v namespace.

```bash
kubectl -n ukf get pods
```

---

### 8. Vymazanie všetkých Podov v namespace `ukf`
Rýchle reštartovanie aplikácií vymazaním všetkých podov v danom namespace (budú automaticky znovu vytvorené Deploymentom).

```bash
kubectl -n ukf delete pods --all
```

---

### 9. Live editácia ConfigMap
Editovanie existujúcej `ConfigMap` s názvom `nginx-config` pomocou interaktívneho editora.

```bash
kubectl edit configmap nginx-config -o yaml -n ukf
```

---

### 10. Pripojenie sa do bežiaceho podu
Pripojenie do podu s menom `nginx-d64b868c9-mvt6h` v namespace `ukf`, do kontajnera `nginx` cez bash terminál.

```bash
kubectl exec -it nginx-d64b868c9-mvt6h -n ukf -c nginx -- bash
```

---

# 1. Vygeneruj nejaký traffic (každý curl pridá riadok do access.log)
curl http://localhost:30080
curl http://localhost:30080
curl http://localhost:30080

# 2. Pozri logy v aktuálnom pode
kubectl -n exam-budis exec deployment/hello-app -- cat /var/log/nginx/access.log
# Uvidíš 3 riadky

# 3. Zabi pod (deployment automaticky vytvorí nový)
kubectl -n exam-budis delete pod -l app=hello-app

# 4. Počkaj sekundu, potom znova pozri logy v NOVOM pode
kubectl -n exam-budis exec deployment/hello-app -- cat /var/log/nginx/access.log
# Tie isté 3 riadky stále tam → dôkaz že prežili reštart podu

# if secret

curl.exe http://localhost:30081/logs
# → HTTP 403

curl.exe -H "X-Token: tajneheslo123" http://localhost:30081/logs
# → HTTP 200, vypíše access.log

curl.exe -H "X-Token: zlytoken" http://localhost:30081/logs
# → HTTP 403