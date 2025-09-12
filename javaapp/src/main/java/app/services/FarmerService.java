package app.services;

import app.entities.Farmer;

import java.util.List;
import java.util.Optional;

public interface FarmerService {
    public Farmer saveFarmer(Farmer farmer);
    Optional<Farmer> findById(Long id);
    Optional<Farmer> findByLogin(String login);
    List<Farmer> findAll();
    void deleteById(Long id);
    Optional<Farmer> getFarmer();
}
