const express = require('express');
const router = express.Router();
const Review = require('../models/Review');
const authMiddleware = require('../middleware/auth');

router.get('/', async (req, res) => {
  const reviews = await Review.find({ approved: true }).sort({ createdAt: -1 });
  res.json(reviews);
});

router.get('/all', authMiddleware, async (req, res) => {
  const reviews = await Review.find().sort({ createdAt: -1 });
  res.json(reviews);
});

router.post('/', async (req, res) => {
  const { jmeno, prijmeni, email, text } = req.body;
  if (!jmeno || !prijmeni || !email || !text)
    return res.status(400).json({ message: 'Vyplňte všechna pole' });

  await Review.create({ jmeno, prijmeni, email, text });
  res.status(201).json({ message: 'Recenze přijata, čeká na schválení' });
});

router.patch('/:id/approve', authMiddleware, async (req, res) => {
  const review = await Review.findByIdAndUpdate(req.params.id, { approved: true }, { new: true });
  if (!review) return res.status(404).json({ message: 'Nenalezeno' });
  res.json(review);
});

router.delete('/:id', authMiddleware, async (req, res) => {
  await Review.findByIdAndDelete(req.params.id);
  res.json({ message: 'Smazáno' });
});

module.exports = router;