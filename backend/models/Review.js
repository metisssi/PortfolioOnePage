const mongoose = require('mongoose');

const reviewSchema = new mongoose.Schema({
  jmeno: { type: String, required: true },
  prijmeni: { type: String, required: true },
  email: { type: String, required: true },
  text: { type: String, required: true },
  approved: { type: Boolean, default: false }
}, { timestamps: true });

module.exports = mongoose.model('Review', reviewSchema);