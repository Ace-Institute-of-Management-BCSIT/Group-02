const express = require('express');
// const mysql = require('mysql2');
const sql = require('mssql');
const bodyParser = require('body-parser');
const session = require('express-session');
const path = require('path');

const app = express();

// Middleware
app.use(bodyParser.urlencoded({ extended: true }));
app.use(express.static(path.join(__dirname, 'public')));
app.use(session({
    secret: 'calculator_secret_key',
    resave: false,
    saveUninitialized: true
}));

// MySQL Connection
// const db = mysql.createConnection({
//     host: 'localhost',
//     user: 'root',       
//     password: '12345', 
//     database: 'calc_db'
// });
const dbConfig = {
    user: 'sa',
    password: 'MyAugustThre0e!3',
    server: 'localhost',
    database: 'calc_db',
    options: {
        encrypt: false,
        trustServerCertificate: true
    }
};
let pool;
sql.connect(dbConfig).then((p) => {
    pool = p;
    console.log('Connected to SQL Server');
}).catch(err => {
    console.error('Database Connection Failed: ', err);
});

// Middleware to protect routes
function isAuthenticated(req, res, next) {
    if (req.session.user) {
        return next();
    }
    res.redirect('/login.html');
}

// Routes
app.get('/', (req, res) => {
    res.redirect('/login.html');
});

// Handle Login
app.post('/login', async (req, res) => {
    const { username, password } = req.body;

    try {
        const result = await pool.request()
            .input('username', sql.VarChar, username)
            .input('password', sql.VarChar, password)
            .query(`
                SELECT * FROM users
                WHERE username = @username
                AND password = @password
            `);

        const results = result.recordset;

        if (results.length > 0) {
            req.session.user = username;
            res.redirect('/calculator');
        } else {
            res.send('<h3>Invalid Username or Password</h3><a href="/login.html">Try Again</a>');
        }

    } catch (err) {
        console.error(err);
        res.send("Database error");
    }
});
// Serve Protected Calculator Page
app.get('/calculator', isAuthenticated, (req, res) => {
    res.sendFile(path.join(__dirname, 'public', 'calculator.html'));
});

// Handle Logout
app.get('/logout', (req, res) => {
    req.session.destroy();
    res.redirect('/login.html');
});

// Start Server
app.listen(3000, () => {
    console.log('Server running on http://localhost:3000');
});