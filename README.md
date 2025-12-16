# Champions League Simulation – Laravel Project

A Laravel-based football simulation engine that generates fixtures, simulates weekly matches, calculates standings, and predicts championship probabilities using Monte Carlo simulations.

---

## 📌 Features

### 🏆 League Simulation
- Generate full-season fixtures.
- Play all matches or advance week-by-week.
- Automatically calculates:
  - Home/away goals  
  - Points  
  - Win/Draw/Loss records  
  - Goal difference  

### 📅 Fixture Management
- Round-robin fixture generation.
- Weekly fixture lists.
- Orchestrated simulation flow via `SimulationOrchestrator`.

### 📈 Championship Prediction
- Monte Carlo–based probability simulation.
- Predicts each team's chance of winning the championship.

### 🖥️ Dashboard UI
- Displays current week fixtures.
- Shows played match results.
- Live standings table (Bootstrap UI).

---

## 📂 Project Structure

```
app/
├── Http/
│   └── Controllers/
│   │   ├── TeamController.php
│   │   ├── FixtureController.php
│   │   └── SimulationController.php
│   └── Resources/
│       ├── FixtureResource.php
│       └── TeamResource.php
├── Orchestrators/
│   └── SimulationOrchestrator.php
├── Services/
│   ├── ChampionshipPredictorService.php
│   ├── FixtureService.php
│   ├── LeagueTableBuilderService.php
│   ├── TeamService.php
│   └── SimulationService.php
├── Repositories/
│   ├── TeamRepository.php
│   ├── GameRepository.php
│   └── FixtureRepository.php
├── Models/
│   ├── Team.php
│   ├── Game.php
│   └── Fixture.php
└── Support/
    └── MathUtils.php
```

---

## ☁️ Cloud Infrastructure (AWS)

The project is deployed on **Amazon Web Services (AWS)** using the following components:

### **🌐 Application Load Balancer (Not Used)**
- The project does **not** use an ALB.
- EC2 directly serves the application through Nginx via port 80.
- **App can be tested at URL:** <a href="http://3.75.91.247" target="_blank">http://3.75.91.247</a>
- *Note: App only accepts http:// to test.*

### **🖥️ Amazon EC2**
- Hosts the Laravel application.
- Runs an Nginx server.
- Exposes the application over **port 80**.
- No Application Load Balancer is used for this setup.
- Handles Composer, PHP, queue workers, and static files.

### **🗄️ Amazon RDS (MySQL)**
- Managed MySQL database instance.
- Automated backups and monitoring.
- Stores all application data: teams, fixtures, games, simulation results.

### **🔐 Security Groups**
The infrastructure uses **two security groups**:

#### **1. RDS Security Group**
- Inbound: **Allows all traffic from anywhere (0.0.0.0/0)** — for testing purposes only.
- Outbound: **Allows all outbound traffic**.
- *Note: This configuration is insecure for production.*

#### **2. EC2 Security Group**
- Inbound: **Port 80 (HTTP)** open to 0.0.0.0/0.
- Inbound: **Port 22 (SSH)** open to 0.0.0.0/0 (testing only).
- Outbound: **Allows all outbound traffic**.
- *Note: This inbound configuration is insecure for production. Would prefer https traffic through ALB*

---

## ⚙️ Core Logic

### **SimulationOrchestrator**
Coordinates:
- Retrieving current week
- Getting fixtures
- Playing weeks
- Syncing results with standings

### **SimulationService**
Handles:
- Goal simulation
- Home advantage calculation
- Final scoreline logic

### **Repositories**
Encapsulate all database interactions:
- Teams  
- Games  
- Fixtures  

### **ChampionshipPredictor**
Runs **N Monte Carlo simulations** to estimate each team’s probability of becoming champion.

---

## 🛠️ Requirements

- PHP 8.2+
- Laravel 12+
- MySQL 8+
- Composer
- Node.js + npm
