const mongoose = require('mongoose');

const contentSchema = new mongoose.Schema({
  sluzby: {
    nadpis: { type: String, default: 'Léčba bolestí zad' },
    text: { type: String, default: '' },
    telefon: { type: String, default: '' },
    email: { type: String, default: '' }
  },
  proc_za_mnou: {
    nadpis: { type: String, default: 'Proč za mnou?' },
    body: [{ type: String }]
  },
  o_mne: {
    nadpis: { type: String, default: 'O mně' },
    text: { type: String, default: '' },
    body: [{ type: String }],
    foto: { type: String, default: '' }
  }
});

module.exports = mongoose.model('Content', contentSchema);