package com.handapp.HandApp.controller;

import org.springframework.stereotype.Controller;
import org.springframework.web.bind.annotation.GetMapping;

@Controller
public class TraductorController {

    @GetMapping("/traductor")
    public String traductor() {
        return "traductor";
    }
}