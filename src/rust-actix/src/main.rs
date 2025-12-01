use actix_web::{get, App, HttpResponse, HttpServer, Responder};

#[get("/{file}")]
async fn hello() -> impl Responder {
    HttpResponse::Ok().body("Hello, world!")
}

#[actix_web::main]
async fn main() -> std::io::Result<()> {
    HttpServer::new(|| {
            App::new()
                .service(hello)
        })
        .bind(("0.0.0.0", 1337))?
        .run()
        .await
}
