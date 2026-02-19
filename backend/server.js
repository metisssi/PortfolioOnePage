require('dotenv').config();
const express = require('express');
const cors = require('cors');
const path = require('path');
const mongoose = require('mongoose');

const app = express();

app.use(cors());
app.use(express.json());
app.use('/uploads', express.static(path.join(__dirname, 'uploads')));

mongoose.connect(process.env.MONGODB_URI)
  .then(() => console.log('MongoDB připojeno'))
  .catch(err => console.error('Mongo chyba:', err));

app.use('/api/auth', require('./routes/auth'));
app.use('/api/content', require('./routes/content'));
app.use('/api/gallery', require('./routes/gallery'));
app.use('/api/reviews', require('./routes/reviews'));

app.get('/api/health', (req, res) => res.json({ status: 'ok' }));

const PORT = process.env.PORT || 5000;
app.listen(PORT, () => console.log(`Server běží na portu ${PORT}`));