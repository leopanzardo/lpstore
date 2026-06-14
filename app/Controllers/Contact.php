<?php

namespace App\Controllers;

class Contact extends BaseController
{
    public function index()
    {
        $viewData = array_merge($this->getBaseData(), [
            'title' => 'Contacto'
        ]);
        
        return view('contact/index', $viewData);
    }
    
    public function send()
    {
        $rules = [
            'name' => 'required|min_length[3]',
            'email' => 'required|valid_email',
            'subject' => 'required|min_length[3]',
            'message' => 'required|min_length[10]'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $name = $this->request->getPost('name');
        $email = $this->request->getPost('email');
        $subject = $this->request->getPost('subject');
        $message = $this->request->getPost('message');
        
        if ($this->sendContactEmail($name, $email, $subject, $message)) {
            return redirect()->to('/contacto')->with('success', 'Mensaje enviado correctamente. Te responderemos a la brevedad.');
        }
        
        return redirect()->back()->with('error', 'Error al enviar el mensaje. Intenta nuevamente.');
    }
    
    private function sendContactEmail($name, $email, $subject, $message)
    {
        $emailService = \Config\Services::email();
        
        $htmlMessage = "
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset='UTF-8'>
                <title>Nuevo mensaje de contacto</title>
                <style>
                    body { font-family: Arial, sans-serif; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background: #4a6cf7; color: white; padding: 20px; text-align: center; }
                    .content { background: #f9f9f9; padding: 20px; }
                    .field { margin-bottom: 15px; }
                    .label { font-weight: bold; color: #333; }
                    .footer { background: #eee; padding: 15px; text-align: center; font-size: 12px; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h2>Nuevo mensaje de contacto</h2>
                    </div>
                    <div class='content'>
                        <div class='field'>
                            <div class='label'>Nombre:</div>
                            <div>" . esc($name) . "</div>
                        </div>
                        <div class='field'>
                            <div class='label'>Email:</div>
                            <div>" . esc($email) . "</div>
                        </div>
                        <div class='field'>
                            <div class='label'>Asunto:</div>
                            <div>" . esc($subject) . "</div>
                        </div>
                        <div class='field'>
                            <div class='label'>Mensaje:</div>
                            <div>" . nl2br(esc($message)) . "</div>
                        </div>
                    </div>
                    <div class='footer'>
                        <p>Este mensaje fue enviado desde el formulario de contacto de " . $this->lpConfig->storeName . "</p>
                    </div>
                </div>
            </body>
            </html>
        ";
        
        $emailService->setTo($this->lpConfig->storeEmail);
        $emailService->setFrom($email, $name);
        $emailService->setReplyTo($email, $name);
        $emailService->setSubject("Contacto: $subject");
        $emailService->setMessage($htmlMessage);
        $emailService->setMailType('html');
        
        return $emailService->send();
    }
}