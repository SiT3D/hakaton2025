package app.controllers;

import app.services.FarmerService;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RestController;

@RestController
@RequestMapping("api")
public class FarmerController {

    @Autowired
    FarmerService farmerService;

    @GetMapping("getFarmerById")
    public String getFarmerById(Long id){
        return "asd";
    }
}
