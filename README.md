# Web Flac Player

---

## Struktur Folder

Pastikan struktur project seperti ini:

```
xampp/htdocs/project-folder
               index.php
               lagu1.flac
               lagu2.flac
               lagu3.flac
```

Nggak pakai album, folder cover, atau auto scan. Semua manual biar gampang dijelasin.

---

## Tambah Lagu?

Di dalam file `index.php`, cari bagian constructor:

```php
public function __construct(){
    $this->songs = [
        ['title' => 'Tarot - Feast', 'url' => 'lagu1.flac'],
        ['title' => 'Judul Lagu 2', 'url' => 'lagu2.flac'],
        ['title' => 'Judul Lagu 3', 'url' => 'lagu3.flac']
    ];
}
```

Yang diganti cuma dua hal:

- `title` → nama yang mau ditampilkan  
- `url` → nama file `.flac` yang ada di folder  

Contoh:

File : `Tarot.flac`

```php
['title' => 'Tarot - Feast', 'url' => 'Tarot.flac']
```

---

### Love Notes <33
- Aku bisa kirim beberapa lagu kalo dibutuhin buat test.
- File yang aku punya kebanyakan berformat `.flac`.
- Ukuran file `.flac` cukup besar, kisaran 20–30MB per lagu.

### Yang Punya : 
- Kevin Nafeeza Daffa / 21120125140124
- Kelompok 34, Shift 5

---