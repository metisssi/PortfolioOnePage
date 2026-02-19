const express = require('express');
const router = express.Router();
const multer = require('multer');
const path = require('path');
const fs = require('fs');
const { v4: uuidv4 } = require('uuid');
const Gallery = require('../models/Gallery');
const authMiddleware = require('../middleware/auth');

const storage = multer.diskStorage({
  destination: (req, file, cb) => cb(null, path.join(__dirname, '../uploads')),
  filename: (req, file, cb) => cb(null, uuidv4() + path.extname(file.originalname))
});
const upload = multer({ storage, limits: { fileSize: 5 * 1024 * 1024 } });

router.get('/', async (req, res) => {
  const gallery = await Gallery.find().sort({ createdAt: -1 });
  res.json(gallery);
});

router.post('/', authMiddleware, upload.single('image'), async (req, res) => {
  const item = await Gallery.create({
    filename: req.file.filename,
    url: `/uploads/${req.file.filename}`,
    popis: req.body.popis || ''
  });
  res.status(201).json(item);
});

router.delete('/:id', authMiddleware, async (req, res) => {
  const item = await Gallery.findByIdAndDelete(req.params.id);
  if (!item) return res.status(404).json({ message: 'Nenalezeno' });

  const filePath = path.join(__dirname, '../uploads', item.filename);
  if (fs.existsSync(filePath)) fs.unlinkSync(filePath);

  res.json({ message: 'Smazáno' });
});

module.exports = router;