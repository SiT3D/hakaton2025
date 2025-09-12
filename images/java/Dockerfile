FROM maven:3.9.9-eclipse-temurin-17 AS build
WORKDIR /app
COPY javaapp /app
RUN mvn clean package -DskipTests

FROM openjdk:17-jdk-slim
WORKDIR /usr/src/app
COPY --from=build /app/target/*.jar app.jar
CMD ["java", "-jar", "app.jar"]
