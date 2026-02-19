require('dotenv').config();
const mongoose = require('mongoose');
const Content = require('./models/Content');

mongoose.connect(process.env.MONGODB_URI).then(async () => {
  await Content.deleteMany({});
  await Content.create({
    sluzby: {
      nadpis: 'Léčba bolestí zad',
      text: '1. Výhřezy plotének\n2. Bechtěrevova nemoc\n3. Skolióza\n4. Skřípnutý nerv v zádech',
      telefon: '+420 123 456 789'
    },
    proc_za_mnou: {
      nadpis: 'Proč za mnou?',
      body: [
        'Metoda nemá vedlejší účinky',
        'Rychlé výsledky',
        'Odstranění příčiny',
        'Záruka',
        'Bezoperační způsob léčení',
        'Léčení bez léků',
        'Léčení bez fyzického namáhání',
        'Relaxační terapie',
        'Terapie na dálku'
      ]
    }
  });

  console.log('✅ Seed hotový!');
  process.exit();
}).catch(err => { console.error(err); process.exit(1); });