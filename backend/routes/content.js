const express = require('express');
const router = express.Router();
const Content = require('../models/Content');
const authMiddleware = require('../middleware/auth.js');

router.get('/', async (req, res) => {
  let content = await Content.findOne();
  if (!content) content = await Content.create({});
  res.json(content);
});

router.put('/', authMiddleware, async (req, res) => {
  let content = await Content.findOne();
  if (!content) content = new Content();
  Object.assign(content, req.body);
  await content.save();
  res.json(content);
});

module.exports = router;