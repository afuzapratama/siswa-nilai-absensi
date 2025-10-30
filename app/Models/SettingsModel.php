<?php

namespace App\Models;

use CodeIgniter\Model;

class SettingsModel extends Model
{
    protected $table            = 'settings';
    protected $primaryKey       = 'id_setting';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = ['setting_key', 'setting_value'];
    protected $useTimestamps    = false;

    // Dates
    // protected $createdField  = 'created_at';
    // protected $updatedField  = 'updated_at';
    // protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules    = [
        // [FIX] Menambahkan aturan untuk id_setting agar placeholder di bawah valid
        'id_setting'    => 'permit_empty|integer',
        'setting_key'   => 'required|is_unique[settings.setting_key,id_setting,{id_setting}]',
        'setting_value' => 'permit_empty'
    ];
    protected $validationMessages   = [
        'setting_key' => [
            'required'  => 'Setting key wajib diisi.',
            'is_unique' => 'Setting key sudah ada.'
        ]
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    // protected $allowCallbacks = true;
    // protected $beforeInsert   = [];
    // protected $afterInsert    = [];
    // protected $beforeUpdate   = [];
    // protected $afterUpdate    = [];
    // protected $beforeFind     = [];
    // protected $afterFind      = [];
    // protected $beforeDelete   = [];
    // protected $afterDelete    = [];


    /**
     * Mengambil semua settings sebagai array [key => value]
     */
    public function getSettings()
    {
        $all_settings = $this->findAll();
        $settings_map = [];
        foreach ($all_settings as $item) {
            $settings_map[$item['setting_key']] = $item['setting_value'];
        }
        return $settings_map;
    }

    /**
     * Mengambil satu value setting berdasarkan key
     */
    public function getSetting($key)
    {
        $result = $this->where('setting_key', $key)->first();
        return $result ? $result['setting_value'] : null;
    }

    /**
     * Menyimpan data settings
     * $data adalah array asosiatif [setting_key => setting_value]
     */
    public function saveSettings($data)
    {
        $this->db->transStart();

        foreach ($data as $key => $value) {
            // Cek jika key sudah ada
            $existing = $this->where('setting_key', $key)->first();

            if ($existing) {
                // Update
                $saveData = [
                    'id_setting'    => $existing['id_setting'], // [FIX] Kirim ID untuk validasi
                    'setting_key'   => $key,
                    'setting_value' => $value
                ];
                if (!$this->update($existing['id_setting'], $saveData)) {
                    // Jika validasi gagal di sini
                    log_message('error', 'Gagal update setting: ' . json_encode($this->errors()));
                }
            } else {
                // Insert
                $saveData = [
                    'setting_key'   => $key,
                    'setting_value' => $value
                ];
                if (!$this->insert($saveData, false)) {
                     // Jika validasi gagal di sini
                    log_message('error', 'Gagal insert setting: ' . json_encode($this->errors()));
                }
            }
        }

        $this->db->transComplete();

        return $this->db->transStatus();
    }
}

