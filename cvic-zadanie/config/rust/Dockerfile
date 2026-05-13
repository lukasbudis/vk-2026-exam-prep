FROM rust:1.85.1 AS builder
WORKDIR /app

COPY ./src/rust-app .
RUN cargo build --release

FROM debian:bookworm-slim
WORKDIR /app
COPY --from=builder /app/target/release/rust-hello-world .
EXPOSE 8080
CMD ["./rust-hello-world"]