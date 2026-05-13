# Cvičné zadanie 1 — Request Log API v Kubernetes

## Popis aplikácie

HTTP API postavené na stock `nginx` image, ktoré pri každom requeste pridá záznam do `access.log`. Endpoint `GET /` vracia plaintext "ok - request logged", `GET /logs` vypisuje obsah log súboru (chránený tokenom v hlavičke `X-Token`).

Nginx natívne loguje každý request do `/var/log/nginx/access.log` v Combined Log Format, takže záznam obsahuje čas, HTTP metódu, cestu, status code, user-agent — viac než zadanie vyžaduje. Log súbor je na perzistentnom PVC, takže prežije reštart podu.

## Komponenty

| Súbor | Účel |
|---|---|
| `configmap.yaml` | 3 env vars (LOG_LEVEL, LOG_DIR, LOG_FILE) + `default.conf` pre nginx (location bloky pre `/` a `/logs`) |
| `secret.yaml` | API token pre autorizáciu `/logs` endpointu |
| `pv.yaml` | hostPath PersistentVolume 1 GiB |
| `pvc.yaml` | claim 10 MiB s `storageClassName: manual` |
| `deployment.yaml` | nginx s envFrom (ConfigMap+Secret), subPath mount pre default.conf, PVC mount na /var/log/nginx/ |
| `svc.yaml` | NodePort 30081 |

## Postup nasadenia

```bash
kubectl create namespace exam-budis-logapi
kubectl apply -f config/k8s-exam/
kubectl -n exam-budis-logapi get pods,pvc,svc,configmap,secret
```

Pod by mal byť `Running`, PVC `Bound`. Po každej zmene ConfigMap/Secret-u vynúť reštart:

```bash
kubectl -n exam-budis-logapi rollout restart deploy/hello-app
```

## Testovanie

```bash
# Generuj traffic (každý request sa zapíše do access.log)
curl.exe http://localhost:30081/
curl.exe http://localhost:30081/

# /logs bez tokenu → 403
curl.exe http://localhost:30081/logs

# /logs so správnym tokenom → 200, vypíše log
curl.exe -H "X-Token: tajneheslo123" http://localhost:30081/logs

# Overenie perzistencie: zmaž pod, počkaj na nový, znova prečítaj logy
kubectl -n exam-budis-logapi delete pod -l app=hello-app
kubectl -n exam-budis-logapi exec deploy/hello-app -- cat /var/log/nginx/access.log
# → predchádzajúce záznamy stále tam → PVC funguje
```

## Vysvetlenie kľúčových rozhodnutí

- **Stock nginx namiesto custom image** — nginx defaultne loguje každý request, čo je presne čo zadanie chce. Žiadny Dockerfile, žiadny build.
- **subPath mount pre default.conf** — mountnúť celý ConfigMap ako adresár by prepísalo `/etc/nginx/conf.d/`. SubPath mountuje len jeden kľúč ako súbor.
- **Token v nginx configu hard-coded + paralelne v Secrete** — nginx config nevie expandovať env vars v `if` direktívach. Pre čistejšiu produkčnú variantu by sa použil InitContainer s `envsubst`.
- **`$http_x_token`** — nginx automaticky parsuje request hlavičky na premenné. Hlavička `X-Token` → premenná `$http_x_token`.

## Splnené body

| Kritérium | Body |
|---|---|
| Namespace a štruktúra | 5 |
| Zmysluplné použitie ConfigMap | 10 |
| Funkčné PV/PVC a korektné mounty | 10 |
| Deployment s aplikáciou | 10 |
| Service NodePort | 10 |
| Aplikácia číta/zapisuje filesystem | 10 |
| README + testovací postup | 5 |
| **Spolu** | **60** |

**Bonus:** Secret + autorizácia `/logs` endpointu (≈ +5–10 b).
