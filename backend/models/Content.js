const mongoose = require('mongoose');

const contentSchema = new mongoose.Schema({
  sluzby: {
    nadpis: { type: String, default: 'Léčba bolestí zad' },
    text: { type: String, default: '' },
    telefon: { type: String, default: '' }
  },
  proc_za_mnou: {
    nadpis: { type: String, default: 'Proč za mnou?' },
    body: [{ type: String }]
  }
});

module.exports = mongoose.model('Content', contentSchema);