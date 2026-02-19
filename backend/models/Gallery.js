const mongoose = require('mongoose');

const gallerySchema = new mongoose.Schema({
  url: { type: String, required: true },      // externí URL obrázku
  popis: { type: String, default: '' }
}, { timestamps: true });

module.exports = mongoose.model('Gallery', gallerySchema);