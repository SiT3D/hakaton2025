package app.repositories;

import app.entities.Farmer;
import org.springframework.data.jpa.repository.JpaRepository;

public interface FarmerRepository extends JpaRepository<Farmer,Long> {
}
