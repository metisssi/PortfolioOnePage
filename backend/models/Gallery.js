const mongoose = require('mongoose');

const gallerySchema = new mongoose.Schema({
  filename: { type: String, required: true },
  url: { type: String, required: true },
  popis: { type: String, default: '' }
}, { timestamps: true });

module.exports = mongoose.model('Gallery', gallerySchema);