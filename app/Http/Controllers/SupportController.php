<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SupportController extends Controller
{
    /**
     * Menampilkan halaman bantuan/support
     */
    public function index()
    {
        return view('support.index');
    }

    /**
     * Menampilkan halaman FAQ
     */
    public function faq()
    {
        return view('support.faq');
    }

    /**
     * Menampilkan halaman kontak
     */
    public function contact()
    {
        return view('support.contact');
    }

    /**
     * Mengirim pesan support
     */
    public function sendMessage(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|min:10',
        ]);

        // Kirim email atau simpan ke database
        $supportEmail = env('SUPPORT_EMAIL', 'support@smkn2semarang.sch.id');

        \Mail::raw(
            "Nama: {$validated['name']}\nEmail: {$validated['email']}\n\nPesan:\n{$validated['message']}",
            function ($message) use ($supportEmail, $validated) {
                $message->to($supportEmail)
                    ->subject("Support: {$validated['subject']}")
                    ->replyTo($validated['email']);
            }
        );

        return back()->with('success', 'Terima kasih! Pesan Anda telah terkirim. Tim support akan menghubungi Anda segera.');
    }
}
