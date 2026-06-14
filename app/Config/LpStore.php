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
    }
}