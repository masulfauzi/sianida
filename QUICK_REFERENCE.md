# Quick Reference: Import Soal Feature

## File Locations
```
Controller:   app/Modules/UjianSemester/Controllers/UjianSemesterController.php
View:         app/Modules/UjianSemester/Views/ujiansemester_upload.blade.php
Routes:       app/Modules/UjianSemester/routes.php
Template:     public/templates/Template_Soal_Semester.xlsx
Model:        app/Modules/SoalSemester/Models/SoalSemester.php
Database:     soal_semester table
```

## Key Implementation Details

### Import Function Signature
```php
public function import(Request $request)
```

### Request Parameters
- `id_ujiansemester` (UUID) - Required
- `file` (Excel) - Required, format .xlsx or .xls

### Excel Column Mapping
```
A=no_soal, B=soal, C=opsi_a, D=opsi_b, E=opsi_c, F=opsi_d, 
G=opsi_e, H=kunci, I=skip, J=gambar, K=skip, 
L=gambar_a, M=gambar_b, N=gambar_c, O=gambar_d, P=gambar_e
```

### Validation Rules
- no_soal: required, unique per ujian
- soal: required, non-empty
- kunci: required, must be A|B|C|D|E
- Opsi A-E: no validation (can be empty)
- Images: optional

## Code Snippets

### Parse Excel
```php
$spreadsheet = IOFactory::load($file);
$worksheet = $spreadsheet->getActiveSheet();
$rows = $worksheet->toArray();
```

### Database Transaction
```php
DB::beginTransaction();
// ... do work ...
DB::commit();
// On error:
DB::rollBack();
```

### Bulk Insert
```php
$chunks = array_chunk($soalData, 100);
foreach ($chunks as $chunk) {
    SoalSemester::insert($chunk);
}
```

### JSON Response
```php
return response()->json([
    'success' => true,
    'message' => 'Success message',
    'count' => $totalInserted
]);
```

## Frontend JS
- Modal ID: `#modalImportSoal`
- Button ID: `#btn-import-soal`
- Form ID: `#formImportSoal`
- File Input: `#file-import`
- Endpoint: `/ujiansemester/import`
- Method: POST

## Testing
```bash
# Test file can be created with:
php /tmp/create_template.php

# Verify route:
php artisan route:list | grep import

# Check method:
php artisan tinker
> method_exists(\App\Modules\UjianSemester\Controllers\UjianSemesterController::class, 'import')
```

## Common Errors & Solutions

| Error | Solution |
|-------|----------|
| File not Excel | Check MIME type, use .xlsx |
| No soal duplicate | Make no_soal unique per ujian |
| Invalid kunci | Use A/B/C/D/E only |
| Transaction fails | Check DB connection |
| Modal not showing | Check jQuery is loaded |

## Performance Tips
- Use chunk insert (100 rows per batch)
- Use transaction for atomicity
- Validate before insert
- Use database transaction for rollback

## Security Checklist
- [x] Middleware auth required
- [x] Authorization check (owner or admin)
- [x] File type validation
- [x] CSRF token required
- [x] Input sanitization (trim)
- [x] UUID validation
- [x] No SQL injection
- [x] No XSS in response

## Browser Compatibility
- Chrome/Edge: ✅ Tested
- Firefox: ✅ Compatible
- Safari: ✅ Compatible
- IE11: ⚠️ May need polyfills

## Dependencies
- PhpOffice/PhpSpreadsheet: ^5.1
- Laravel: 9.x
- PHP: 8.0+
- jQuery: (for modal/AJAX)
- Bootstrap: 4.x+ (for modal styles)

## Migration Path (if needed)
```php
// If adding new columns to soal_semester:
Schema::table('soal_semester', function (Blueprint $table) {
    $table->string('new_column')->nullable();
});

// Then update import() function to handle the new column
```

## Logs
Imports are logged at:
- Database: `log` table with action 'mengimport soal ujian semester'
- Can track: ujian ID, total soal imported, timestamp, user

---

**Last Updated:** 19 Mei 2026
