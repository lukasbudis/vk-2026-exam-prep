# Cvičné zadanie 3 — Web zo šablóny s InitContainerom

## Popis aplikácie

Statický web ktorého obsah sa generuje **pri štarte podu** z hodnôt v ConfigMape pomocou InitContainera. Hlavný kontajner (nginx) následne servíruje vygenerovaný `index.html` zo zdieľaného PVC.

Tri konfiguračné hodnoty z ConfigMap-u určujú výsledný obsah:
- `SITE_TITLE` — nadpis stránky
- `MESSAGE` — text v body
- `THEME` — farba pozadia (CSS color/hex)

Pri zmene ConfigMap-u a reštarte podu InitContainer vygeneruje nový HTML — demonštruje "konfigurovateľnosť cez ConfigMap".

## Komponenty

| Súbor | Účel |
|---|---|
| `configmap.yaml` | 3 env vars (SITE_TITLE, MESSAGE, THEME) |
| `pv.yaml` | hostPath PersistentVolume, `/mnt/template-data` |
| `pvc.yaml` | claim 10 MiB |
| `deployment.yaml` | InitContainer (busybox) generuje index.html z heredoc šablóny s env var expansion; hlavný container nginx servíruje obsah |
| `svc.yaml` | NodePort 30083 |

## Postup nasadenia

```bash
kubectl create namespace exam-budis-template
kubectl apply -f config/k8s-exam/
kubectl -n exam-budis-template get pods,pvc,svc,configmap
```

InitContainer status v pode: `Init:0/1` → `Init:1/1` → `Running`. Logy InitContainera:
```bash
kubectl -n exam-budis-template logs deploy/hello-app -c render-template
```

## Testovanie

```bash
# Otvor v browseri
http://localhost:30083/

# Alebo cez CLI
curl.exe http://localhost:30083/
# → vidíš vygenerovaný HTML s SITE_TITLE/MESSAGE/THEME hodnotami

# Over že index.html naozaj vznikol na PVC (zo strany hlavného containera)
kubectl -n exam-budis-template exec deploy/hello-app -- cat /usr/share/nginx/html/index.html

# Zmeň ConfigMap a redeploynij — InitContainer vygeneruje nový obsah
kubectl -n exam-budis-template edit configmap app-config
# zmeň napríklad THEME na "#8b1538"
kubectl -n exam-budis-template rollout restart deploy/hello-app
# počkaj kým je pod Running, znova načítaj http://localhost:30083/
```

## Vysvetlenie kľúčových rozhodnutí

- **InitContainer ako busybox** — minimalistický image s `sh`, žiadny build. Heredoc syntax v shelle (`<<EOF`) podporuje env var expansion (`${SITE_TITLE}`) a command substitution (`$(date)`).
- **PVC zdieľaná medzi initContainer a containers** — oba mountujú ten istý `web-content` volume. InitContainer ho mountuje ako `/work` (zapisuje), main container ako `/usr/share/nginx/html` (číta). Ten istý fyzický volume, dve cesty.
- **`envFrom` na InitContaineri** — InitContainer dostáva všetky env vars z ConfigMap-u, čím shell v `command:` vie expandovať `${SITE_TITLE}` atď.
- **Heredoc s `<<EOF`** (nie `<<'EOF'`) — neuvádzané EOF aktivuje parameter expansion. Quoted (`<<'EOF'`) by ponechalo `${VAR}` literálne.
- **`$(date)` v generovanom HTML** — slúži ako vizuálny dôkaz že obsah vznikol pri konkrétnom štarte podu (každý reštart podu = nový timestamp).

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

**Bonus splnené:**
- ✓ Konfigurovateľná téma podľa ConfigMap (`THEME` ovplyvňuje CSS background)
- ✓ Logika pre zmenu správania podľa ConfigMap

Nedopracované bonusy (ak ostane čas):
- Secret v šablóne ako maskovaná hodnota
- readinessProbe na GET /
