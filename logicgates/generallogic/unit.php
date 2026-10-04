<?php
class Unit {
    private DBconnection $db;

    public function __construct(DBconnection $db) {
        $this->db = $db;
    }

    public function getAll(): Respon {
        $query = "
            SELECT u.*, ku.nama as nama_kategori 
            FROM unit u 
            LEFT JOIN kategori_unit ku ON u.kategori_unit = ku.id 
            WHERE u.is_active = true
        ";
        return $this->db->send_query($query);
    }
    public function create(
        string $nama,
        int $jumlahUnit,
        int $kategoriId,
        string $deskripsi,
        ?string $foto,
        int $adminId
    ): Respon {
        $query = "INSERT INTO unit (nama, jumlah_unit, kategori_unit, deskripsi, foto, created_by)
                  VALUES ($1, $2, $3, $4, $5, $6)";
        return $this->db->send_query($query, [
            $nama,
            $jumlahUnit,
            $kategoriId > 0 ? $kategoriId : null,
            $deskripsi,
            $foto,
            $adminId,
        ]);
    }
 
    // $foto null -> foto lama dipertahankan
    public function update(
        int $id,
        string $nama,
        int $jumlahUnit,
        int $kategoriId,
        string $deskripsi,
        ?string $foto,
        int $adminId
    ): Respon {
        $query = "UPDATE unit
                  SET nama = $1, jumlah_unit = $2, kategori_unit = $3, deskripsi = $4,
                      foto = COALESCE($5::varchar, foto), updated_by = $6
                  WHERE id = $7";
        return $this->db->send_query($query, [
            $nama,
            $jumlahUnit,
            $kategoriId > 0 ? $kategoriId : null,
            $deskripsi,
            $foto,
            $adminId,
            $id,
        ]);
    }
 
    public function delete(int $id): Respon {
        return $this->db->send_query("DELETE FROM unit WHERE id = $1", [$id]);
    }
 
    public function setActive(int $id, bool $aktif, int $adminId): Respon {
        $query = "UPDATE unit SET is_active = $1::boolean, updated_by = $2 WHERE id = $3";
        return $this->db->send_query($query, [$aktif ? 'true' : 'false', $adminId, $id]);
    }
}
?>