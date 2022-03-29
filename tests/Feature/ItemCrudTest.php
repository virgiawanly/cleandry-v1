<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemCrudTest extends TestCase
{
    use RefreshDatabase;

    public function login_user()
    {
        $user = User::factory()->create([
            'role' => 'admin'
        ]);

        $this->actingAs($user);
    }

    /**
     * Test apakah user bisa mengakses halaman login.
     *
     * @return void
     */
    public function test_visit_items_page()
    {
        $this->login_user();

        $response = $this->get('/items');

        $response->assertStatus(200);
    }

    /**
     * Test apakah user bisa membuat data barang baru.
     *
     * @return void
     */
    public function test_create_new_item()
    {
        $this->login_user();

        $newItem = Item::factory()->make();

        $this->post('/items', $newItem->toArray());

        $this->assertDatabaseHas('items', [
            'name' => $newItem->name,
            'status' => $newItem->status,
            'buy_date' => $newItem->buy_date,
        ]);
    }

    /**
     * Test apakah user bisa mengupdate data barang.
     *
     * @return void
     */
    public function test_update_existing_item()
    {
        $this->login_user();

        $newItem = Item::factory()->create();
        $newItem->name = "Update Item";

        $this->put('/items/' . $newItem->id, $newItem->toArray());

        $this->assertDatabaseHas('items', [
            'id' => $newItem->id,
            'name' => "Update Item"
        ]);
    }

    /**
     * Test apakah user bisa menghapus data barang.
     *
     * @return void
     */
    public function test_delete_existing_item()
    {
        $this->login_user();

        $newItem = Item::factory()->create();

        $this->delete('/items/' . $newItem->id);

        $this->assertDatabaseMissing('items', [
            'id' => $newItem->id,
        ]);
    }

    /**
     * Test export data ke file excel.
     *
     * @return void
     */
    public function test_export_to_excel()
    {
        $this->login_user();

        $this->get('/items/export/excel')->assertDownload();
    }

    /**
     * Test export data ke file pdf.
     *
     * @return void
     */
    public function test_export_to_pdf()
    {
        $this->login_user();

        $this->get('/items/export/pdf')->assertStatus(200);
    }

    /**
     * Test download file template excel untuk import.
     *
     * @return void
     */
    public function test_download_import_template()
    {
        $this->login_user();

        $this->get('/download/template/items')->assertDownload();
    }
}
