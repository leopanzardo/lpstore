<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class LpStore extends BaseConfig
{
    // Store Information
    public string $storeName = 'LP Store';
    public string $storeDescription = 'Tu tienda online de confianza';
    public string $storeLogo = '';
    public string $storeEmail = 'email@dominio.com';
    
    // SEO
    public string $metaDescription = 'Encuentra los mejores productos en LP Store';
    public string $metaKeywords = 'tienda, productos, compras online, Uruguay';
    
    // Sucursales (JSON)
    public array $branches = [];
    
    // Social Media
    public string $facebookUrl = '';
    public string $instagramUrl = '';
    public string $twitterUrl = '';
    public string $youtubeUrl = '';

    // Google Maps
    public string $googleMapsApiKey = '';
    
    // API
    public string $apiKey = '';

    // Mercado Pago
    public string $mercadopagoPublicKey = '';
    
    public array $logos = [];
    
    // Constructor para cargar desde .env
    public function __construct()
    {
        parent::__construct();
        
        // Cargar valores desde variables de entorno
        $this->storeName = getenv('Config\LpStore.storeName') ?: $this->storeName;
        $this->storeDescription = getenv('Config\LpStore.storeDescription') ?: $this->storeDescription;
        $this->storeLogo = getenv('Config\LpStore.storeLogo') ?: $this->storeLogo;
        $this->storeEmail = getenv('Config\LpStore.storeEmail') ?: $this->storeEmail;
        $this->metaDescription = getenv('Config\LpStore.metaDescription') ?: $this->metaDescription;
        $this->metaKeywords = getenv('Config\LpStore.metaKeywords') ?: $this->metaKeywords;
        $this->facebookUrl = getenv('Config\LpStore.facebookUrl') ?: $this->facebookUrl;
        $this->instagramUrl = getenv('Config\LpStore.instagramUrl') ?: $this->instagramUrl;
        $this->twitterUrl = getenv('Config\LpStore.twitterUrl') ?: $this->twitterUrl;
        $this->youtubeUrl = getenv('Config\LpStore.youtubeUrl') ?: $this->youtubeUrl;
        $this->googleMapsApiKey = getenv('Config\LpStore.googleMapsApiKey') ?: $this->googleMapsApiKey;
        $this->apiKey = getenv('Config\LpStore.apiKey') ?: $this->apiKey;
        $this->mercadopagoPublicKey = getenv('MERCADOPAGO_PUBLIC_KEY') ?: $this->mercadopagoPublicKey;
        
        // Decodificar el JSON de branches
        $branchesJson = getenv('Config\LpStore.branches');
        if ($branchesJson) {
            $branchesArray = json_decode($branchesJson, true);
            if (is_array($branchesArray)) {
                $this->branches = $branchesArray;
            }
        }
        
        // Detectar automáticamente todos los logos en assets/images/
        $this->detectLogos();
    }
    
    /**
     * Detecta automáticamente todos los logos disponibles en assets/images/
     * Busca archivos que empiecen con "logo" (logo.png, logo.webp, logo.jpg, etc.)
     */
    private function detectLogos(): void
    {
        $logosPath = FCPATH . 'assets/images/';
        $this->logos = [];
        
        if (!is_dir($logosPath)) {
            return;
        }
        
        $files = scandir($logosPath);
        $logoPattern = '/^logo\.[a-zA-Z0-9]+$/';
        
        foreach ($files as $file) {
            if (preg_match($logoPattern, $file)) {
                $extension = pathinfo($file, PATHINFO_EXTENSION);
                $this->logos[] = [
                    'file' => $file,
                    'path' => 'assets/images/' . $file,
                    'type' => $extension,
                    'mime' => $this->getMimeType($extension),
                    'size' => filesize($logosPath . $file)
                ];
            }
        }
        
        // Ordenar por preferencia: webp > avif > jpg > png > otros
        usort($this->logos, function($a, $b) {
            $order = ['webp' => 1, 'avif' => 2, 'jpg' => 3, 'jpeg' => 4, 'png' => 5];
            $aOrder = $order[$a['type']] ?? 99;
            $bOrder = $order[$b['type']] ?? 99;
            return $aOrder <=> $bOrder;
        });
    }
    
    private function getMimeType($extension): string
    {
        return match ($extension) {
            'webp' => 'image/webp',
            'avif' => 'image/avif',
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            default => 'application/octet-stream'
        };
    }
    
    /**
     * Obtiene el mejor logo disponible (prioridad: webp > avif > jpg > png)
     */
    public function getBestLogo(): ?array
    {
        return $this->logos[0] ?? null;
    }
    
    /**
     * Obtiene el logo en un formato específico
     */
    public function getLogoByType($type): ?array
    {
        foreach ($this->logos as $logo) {
            if ($logo['type'] === $type) {
                return $logo;
            }
        }
        return null;
    }
}