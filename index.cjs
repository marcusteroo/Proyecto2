const fs = require('fs');
const { Client, LocalAuth } = require('whatsapp-web.js');
const express = require('express');
const cors = require('cors');
const qrcode = require('qrcode-terminal');

const app = express();
app.use(express.json());
app.use(cors());

// Usa LocalAuth para manejar automáticamente la sesión
const client = new Client({
    authStrategy: new LocalAuth({
        clientId: "client-one" // Cambia el ID si necesitas múltiples clientes
    })
});

client.on('qr', (qr) => {
    qrcode.generate(qr, { small: true });
    console.log('Escanea el código QR con tu móvil para vincular.');
});

client.on('ready', () => {
    console.log('WhatsApp Web está listo');
});

client.on('authenticated', () => {
    console.log('Sesión autenticada');
});

client.on('auth_failure', (message) => {
    console.error('Error de autenticación:', message);
});

client.on('disconnected', (reason) => {
    console.log('Cliente desconectado:', reason);
    client.initialize(); // Intenta reconectar automáticamente
});

// Ruta para enviar mensajes
app.post('/send-message', async (req, res) => {
    const { number, message } = req.body;

    try {
        const chatId = `${number}@c.us`;
        await client.sendMessage(chatId, message);
        res.status(200).send({ success: true, message: 'Mensaje enviado' });
    } catch (error) {
        console.error('Error al enviar el mensaje:', error.message);
        res.status(500).send({ success: false, error: error.message });
    }
});

// Inicia el servidor
const PORT = 3001;
app.listen(PORT, () => console.log(`Servidor Node.js en el puerto ${PORT}`));

// Inicializa el cliente
client.initialize();
