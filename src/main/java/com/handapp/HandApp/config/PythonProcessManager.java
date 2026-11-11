package com.handapp.HandApp.config;

import jakarta.annotation.PostConstruct;
import jakarta.annotation.PreDestroy;
import org.springframework.stereotype.Component;

import java.io.File;
import java.io.IOException;

@Component
public class PythonProcessManager {

    private Process procesoPython;

    @PostConstruct
    public void iniciar() {
        try {
            File carpetaPython = new File("src/main/PYTHON");
            String pythonBin = new File(System.getProperty("user.home"),
                    "AppData/Local/Python/pythoncore-3.12-64/python.exe").getAbsolutePath();
            if (!new File(pythonBin).exists()) {
                pythonBin = "python";
            }
            ProcessBuilder pb = new ProcessBuilder(
                    pythonBin, "manage.py", "runserver", "0.0.0.0:5000", "--noreload"
            );
            pb.directory(carpetaPython);
            pb.inheritIO();
            procesoPython = pb.start();
            System.out.println("Servidor Django + IA (traductor de señas LSC) iniciado en http://localhost:5000");
        } catch (IOException e) {
            System.err.println("No se pudo iniciar el servidor Django/Python: " + e.getMessage());
        }
    }

    @PreDestroy
    public void detener() {
        if (procesoPython != null && procesoPython.isAlive()) {
            procesoPython.destroy();
            System.out.println("Servidor Django + IA detenido.");
        }
    }
}
