package app.services.impl;


import app.entities.Farmer;
import app.repositories.FarmerRepository;
import app.services.FarmerService;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.security.core.Authentication;
import org.springframework.security.core.context.SecurityContextHolder;
import org.springframework.stereotype.Service;

import java.util.List;
import java.util.Optional;

@Service
public class FarmerServiceImpl implements FarmerService {

    private final FarmerRepository repository;

    @Autowired
    public FarmerServiceImpl(FarmerRepository repository) {
        this.repository = repository;
    }


    @Override
    public Farmer saveFarmer(Farmer farmer) {
        return repository.save(farmer);
    }

    @Override
    public Optional<Farmer> findById(Long id) {
        return repository.findById(id);
    }

    @Override
    public Optional<Farmer> findByLogin(String login) {
        return Optional.empty();
    }

    @Override
    public List<Farmer> findAll() {
        return repository.findAll();
    }

    @Override
    public void deleteById(Long id) {
        repository.deleteById(id);
    }

    @Override
    public Optional<Farmer> getFarmer() {
        Authentication auth = SecurityContextHolder.getContext().getAuthentication();
        return Optional.empty();
    }
}

