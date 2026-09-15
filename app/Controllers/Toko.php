<?php
namespace App\Controllers;
use App\Models\StoreModel;

class Toko extends BaseController
{
    public function index()
    {
        $session = session();
        if ($session->get('role') != 'Owner' || !$session->get('isLoggedIn')) {
            return redirect()->to('dashboard');
        }

        $storeModel = new StoreModel();

        $data = [
            'username' => $session->get('username'),
            'toko'     => $storeModel->first() // Ambil baris pertama dari tabel
        ];

        return view('Toko/index', $data);
    }

    public function update()
 {
     $storeModel = new StoreModel();
     $id = $this->request->getPost('id');

         if (! ctype_digit((string) $id)
             || trim((string) $this->request->getPost('store_name')) === ''
             || trim((string) $this->request->getPost('address')) === ''
             || ($this->request->getPost('email') !== '' && ! filter_var($this->request->getPost('email'), FILTER_VALIDATE_EMAIL))) {
             return redirect()->to('toko')->with('error', 'Data profil toko tidak valid.');
         }

     $data = [
         'store_name'  => $this->request->getPost('store_name'),
         'address'     => $this->request->getPost('address'),
         'phone'       => $this->request->getPost('phone'),
         'email'       => $this->request->getPost('email'),
         'footer_nota' => $this->request->getPost('footer_nota')
     ];

     // LOGIKA UPLOAD LOGO YANG LEBIH AMAN
     $fileLogo = $this->request->getFile('logo');

     // getError() == 4 artinya tidak ada file yang diupload. 
     // Jadi, jalankan ini HANYA jika ada file baru yang diupload.
     if ($fileLogo && $fileLogo->getError() !== UPLOAD_ERR_NO_FILE) {
         $allowedMimeTypes = ['image/jpeg', 'image/png'];
         if (! $fileLogo->isValid() || ! in_array($fileLogo->getMimeType(), $allowedMimeTypes, true)
             || $fileLogo->getSizeByUnit('mb') > 2) {
             return redirect()->to('toko')->with('error', 'Logo harus JPG/PNG dan maksimal 2 MB.');
         }

         $namaLogo = $fileLogo->getRandomName(); // Generate nama acak
         $uploadPath = FCPATH . 'uploads/logo/';
         if (! is_dir($uploadPath)) {
             mkdir($uploadPath, 0755, true);
         }
         $fileLogo->move($uploadPath, $namaLogo);
         $data['logo'] = $namaLogo; // Masukkan nama file ke array database
     }

     $storeModel->update($id, $data);
    $this->auditLog('store.updated', 'store', (string) $id);
     session()->setFlashdata('sukses', 'Profil Toko beserta Logo berhasil diperbarui!');
     return redirect()->to('toko');
 }
}