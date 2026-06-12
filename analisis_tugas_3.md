# Analisis Tugas 3 — Integrasi Service ke Cloud Pusat

**Nama**: Didit Aditya  
**NIM**: 102022400066  
**Service**: Vehicle Service (Data Kendaraan)

---

Pada probis kami dimana PENCATATAN OPERASIONAL (Pengisian BBM), aktivitas (DATA KENDARAAN) saya merupakan aktivitas yang cukup kritis pada probis tersebut. Ini merupakan proses penambahan data kendaraan baru yang menyimpan status kendaraan tersebut, berguna untuk mengecek apakah mobil sedang active/inactive untuk proses pencatatan BBM. Service ini berkomunikasi juga dengan SOAP audit untuk mencetak log dan RabbitMQ untuk menyebarkan notifikasi agar dapat dilihat oleh service lain (teman saya nantinya) dan ada sebuah isi dari data-data tersebut yang dapat dilihat secara terbuka oleh sistem. 

Lebih jelasnya, pada SOAP, service mengirimkan data kendaraan baru ditambahkan ke sistem pusat dalam format XML. Isi yang dikirim mencakup TEAM-07 nama aktivitas dan detail dari kendaraan. Setelah itu sistem akan memproses log tersebut dan mendapatkan nomor resi (ReceiptNumber) sebagai bukti bahwa proses telah berhasil. Nah receipt nomor ini menjadikan identitas unik dari kendaraan tersebut (bukti audit dari pusat).

Sedangkan untuk RabbitMQ, setelah data kendaraan berhasil disimpan service akan mengirimkan notifikasi event ke message broker pusat. Tujuannya supaya service milik teman-teman bisa tahu kalau ada kendaraan yang masuk ke sistem tanpa manual checking. Perbedaan SOAP dan RabbitMQ ini jika proses gagal SOAP maka proses gagal dan tidak memasukkan data ke database tapi RabbitMQ gagal proses kendaraan tetap masuk ke database.

Kesimpulannya, keterlibatan SOAP dan RabbitMQ inilah yang membuat transaksi penambahan kendaraan menjadi sangat kritis. Operasi ini tidak bisa berdiri sendiri; ia sangat bergantung pada SOAP untuk legalitas data (audit pusat) sebelum data boleh disimpan, dan sekaligus memikul tanggung jawab untuk menyuapi RabbitMQ agar siklus operasional di *service* lain bisa langsung berjalan. Keterikatan dua arah ini—yakni wajib menunggu validasi "izin" dari luar melalui SOAP, dan setelahnya harus memberikan "trigger" ke luar melalui RabbitMQ—menjadikan transaksi ini titik paling vital yang menghubungkan *service* kendaraan dengan keseluruhan ekosistem bisnis.

### Sequence Diagram Alur SSO & Transaksi
![Sequence Diagram](SEQUENCE%20DIAGRAM%20IAE%20TUGAS%203.png)