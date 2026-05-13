# Cvičné zadanie 2 — Mini Upload Galéria v Kubernetes

## Popis aplikácie

Jednoduchá PHP webová aplikácia (~40 riadkov) ktorá umožní:
- nahrať súbor cez HTML formulár (POST multipart/form-data)
- zobraziť zoznam súborov uložených na perzistentnom úložisku

PHP kód číta tri konfiguračné hodnoty z ConfigMap-u cez `getenv()`:
- `UPLOAD_DIR` — kam ukladať uploadnuté súbory
- `MAX_UPLOAD_MB` — limit veľkosti
- `APP_TITLE` — titulok stránky

Aplikácia validuje veľkosť uploadnutého súboru a sanitizuje meno (path-traversal protection).

## Komponenty

| Súbor | Účel |
|---|---|
| `configmap.yaml` | 3 env vars + `index.php` ako súbor v `data:` |
| `pv.yaml` | hostPath PersistentVolume, `/mnt/upload-data` |
| `pvc.yaml` | claim 100 MiB |
| `deployment.yaml` | `php:apache` image, envFrom, subPath mount pre index.php, PVC mount pre uploads, InitContainer pre chmod + README.txt |
| `svc.yaml` | NodePort 30082 |

## Postup nasadenia

```bash
kubectl create namespace exam-budis-upload
kubectl apply -f config/k8s-exam/
kubectl -n exam-budis-upload get pods,pvc,svc,configmap
```

InitContainer chmodne PVC mount na 777 (Apache user `www-data` potrebuje write permissions) a vytvorí ukážkový `README.txt` v upload adresári ak ešte neexistuje.

## Testovanie

```bash
# Browser
http://localhost:30082/         # vidíš formulár + zoznam súborov

# CLI upload
curl.exe -F "file=@C:/Users/shoot/Pictures/test.jpg" http://localhost:30082/
curl.exe http://localhost:30082/    # test.jpg by mal byť v zozname

# Perzistencia po reštarte podu
kubectl -n exam-budis-upload delete pod -l app=hello-app
# počkaj 10 sekúnd
curl.exe http://localhost:30082/    # súbory stále tam
```

## Vysvetlenie kľúčových rozhodnutí

- **`php:apache` namiesto vlastného Dockerfile** — image má out-of-the-box Apache + mod_php, žiadny build. PHP scripty sa exekuujú automaticky v koreni `/var/www/html/`.
- **PHP namiesto nginx** — nginx alone nevie prijať POST file upload bez prídavných modulov. PHP cez `$_FILES` to rieši v 3 riadkoch.
- **InitContainer s chmod 777** — hostPath PV má default permissions `root:root 755`. Apache user `www-data` by nemohol zapisovať. InitContainer s `chmod 777` to opraví pri každom štarte podu. Súčasne vytvorí ukážkový README.txt — splnenie bonusu.
- **subPath mount pre index.php** — namountuje len jeden kľúč z ConfigMap-u ako súbor, ostatné kľúče (env vars) sa do file mountu neprejavia.
- **Sanitizácia mena súboru** (`preg_replace('/[^A-Za-z0-9._-]/', '_', ...)`) — zabráni path traversal útokom (užívateľ by nemohol uploadnúť súbor s menom `../../etc/passwd`).

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

**Bonus:** InitContainer (vytvorí README.txt + chmod) ≈ +5 b.

## Poznámky

- PHP default `upload_max_filesize=2M` ovplyvňuje hraničný case — pre súbory >2 MB by bolo treba pridať vlastný `php.ini` cez ConfigMap mount, aj keby `MAX_UPLOAD_MB` v env bolo vyššie.
- `chmod 777` v InitContaineri je zjednodušenie pre demo. V produkcii by sa použil `securityContext.fsGroup` s appropriate user/group ID.
