const express = require('express');
const router = express.Router();
const Gallery = require('../models/Gallery');
const authMiddleware = require('../middleware/auth');

router.get('/', async (req, res) => {
  const gallery = await Gallery.find().sort({ createdAt: -1 });
  res.json(gallery);
});

router.post('/', authMiddleware, async (req, res) => {
  const { url, popis } = req.body;
  if (!url) return res.status(400).json({ message: 'URL je povinné' });

  const item = await Gallery.create({ url, popis: popis || '' });
  res.status(201).json(item);
});

router.delete('/:id', authMiddleware, async (req, res) => {
  const item = await Gallery.findByIdAndDelete(req.params.id);
  if (!item) return res.status(404).json({ message: 'Nenalezeno' });
  res.json({ message: 'Smazáno' });
});

module.exports = router;