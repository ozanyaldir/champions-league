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
│       └── SimulationController.php
├── Services/
│   └── SimulationService.php
├── Orchestrators/
│   └── SimulationOrchestrator.php
├── Repositories/
│   ├── TeamRepository.php
│   ├── GameRepository.php
│   └── FixtureRepository.php
└── Simulation/
    └── ChampionshipPredictor.php
```

---

## 🚀 Installation

### 1. Clone the Repository
```bash
git clone <your-repo-url>
cd champions-league
```

### 2. Install Dependencies
```bash
composer install
npm install && npm run dev
```

### 3. Create Environment File
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Configure Database

Update `.env`:

```
DB_DATABASE=champions
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Run Migrations & Seeders
```bash
php artisan migrate --seed
```

### 6. Start Development Server
```bash
php artisan serve
```

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

## 🧪 Running Simulations

### Play All Matches
```
/simulation/play-all
```

### Play Next Week
```
/simulation/play-next-week
```

### Start a New Simulation
```
/simulation/start
```

---

## 🛠️ Requirements

- PHP 8.2+
- Laravel 12+
- MySQL 8+
- Composer
- Node.js + npm

---

## 🤝 Contributing

Pull requests are welcome.  
For major changes, open an issue to discuss proposed modifications.

---

## 📄 License

MIT License.

