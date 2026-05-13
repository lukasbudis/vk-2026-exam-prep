use actix_web::{get, App, HttpResponse, HttpServer, Responder};
use std::fs::File;
use std::io::Write;

#[get("/")]
async fn download_image() -> impl Responder {
    let image_url = "http://nginx/index.php"; // zmeň podľa potreby

    match reqwest::get(image_url).await {
        Ok(response) => {
            if response.status().is_success() {
                let bytes = match response.bytes().await {
                    Ok(data) => data,
                    Err(e) => return HttpResponse::InternalServerError().body(format!("Chyba čítania dát: {}", e)),
                };

                let mut file = match File::create("/tmp/downloaded_image.jpg") {
                    Ok(f) => f,
                    Err(e) => return HttpResponse::InternalServerError().body(format!("Chyba vytvárania súboru: {}", e)),
                };

                if let Err(e) = file.write_all(&bytes) {
                    return HttpResponse::InternalServerError().body(format!("Chyba zápisu do súboru: {}", e));
                }

                HttpResponse::Ok().body("Obrázok úspešne stiahnutý a uložený.")
            } else {
                HttpResponse::BadGateway().body("Obrázok sa nepodarilo stiahnuť.")
            }
        }
        Err(e) => HttpResponse::InternalServerError().body(format!("Chyba HTTP požiadavky: {}", e)),
    }
}

#[actix_web::main]
async fn main() -> std::io::Result<()> {
    println!("Server beží na http://0.0.0.0:8080");
    HttpServer::new(|| App::new().service(download_image))
        .bind(("0.0.0.0", 8080))?
        .run()
        .await
}
