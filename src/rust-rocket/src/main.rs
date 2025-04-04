use std::path::PathBuf;

#[macro_use] extern crate rocket;

#[get("/<_file>")]
fn index(_file: PathBuf) -> String {
    "Hello, world!".to_string()
}

#[launch]
fn rocket() -> _ {
    rocket::build().mount("/", routes![index])
}
