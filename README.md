# BStorm  

Uputstvo za instalaciju i pokretanje aplikacije

## Instalacija projekta

### Preduslovi:

- Instaliran [GIT CLI](https://git-scm.com/)
- Instaliran [Composer](https://getcomposer.org/download/)  
- Instaliran [Docker](https://www.docker.com/products/docker-desktop)

---

Kloniramo repo komandom

```bash
git clone https://github.com/GaGiiiii/BStr/
```

Otvaramo terminal, zatim kucamo:

```bash
cd BStr
composer i
```

komandama iznad smo instalirali sve dependecy-e koji se nalaze u composer.json fajlu

---

## Pokretanje projekta

### Pokretanje backend-a

Backend pokrecemo iz BStr foldera komandom:

```bash
docker compose up
```

Ova komanda će pokrenuti docker container namenjen za development.    
  
U njemu je server pokrenut preko komande php artisan serve   
Osluškuju se promene u kodu, nakon svake promene, aplikacija će se automatski porkenuti ponovo.

Backend radi na [localhost:8000](http://localhost:8000/)   
