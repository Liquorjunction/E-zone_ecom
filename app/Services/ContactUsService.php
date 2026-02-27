<?php

namespace App\Services;

use App\Repositories\Client\Interfaces\ContactUsRepositoryInterface;
use App\Http\Requests\Client\ContactUs\StoreContactUsRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Intervention\Image\Laravel\Facades\Image;

class ContactUsService
{
    protected ContactUsRepositoryInterface $contactUsRepository;

    public function __construct(ContactUsRepositoryInterface $contactUsRepository)
    {
        $this->contactUsRepository = $contactUsRepository;
    }

    public function submitContactForm(StoreContactUsRequest $request): mixed
    {
        return DB::transaction(function () use ($request) {

            $data        = $request->validated();
            $images      = [];
            $attachments = [];

            if (!empty($data['attachments']) && is_array($data['attachments'])) {
                foreach ($data['attachments'] as $file) {
                    if (!$file instanceof UploadedFile) {
                        continue;
                    }

                    $mime = $file->getMimeType();

                    if (str_starts_with($mime, 'image/')) {
                        $filename = uniqid('contact_img_') . '.jpg';
                        $path     = 'contact_attachments/images/' . $filename;

                        $encoded = Image::read($file)
                            ->orient()
                            ->scaleDown(width: 1920)
                            ->toJpeg(quality: 80)
                            ->toString();
                        Storage::disk('public')->put($path, $encoded);

                        $images[] = [
                            'path'          => $path,
                            'original_name' => $file->getClientOriginalName(),
                            'mime'          => 'image/jpeg',
                            'size'          => Storage::disk('public')->size($path),
                        ];

                    } else {
                        $path = $file->store('contact_attachments/docs', 'public');

                        $attachments[] = [
                            'path'          => $path,
                            'original_name' => $file->getClientOriginalName(),
                            'mime'          => $mime,
                            'size'          => $file->getSize(),
                        ];
                    }
                }
            }

            $contactData = [
                'user_id'      => $request->user()?->id,
                'name'         => $data['name'],
                'email'        => $data['email'],
                'mobile'       => $data['mobile'],
                'subject'      => $data['subject'],
                'order_number' => $data['order_number'] ?? null,
                'product_sku'  => $data['product_sku']  ?? null,
                'message'      => $data['message'],
                'images'       => !empty($images)       ? $images       : null,
                'attachments'  => !empty($attachments)  ? $attachments  : null,
                'status'        => 'open',
                'admin_id'      => null,
                'admin_notes'   => null,
            ];

            $contact = $this->contactUsRepository->createContactUs($contactData);
            $prefix = config('app.PREFIX');
            $idPart = str_pad($contact->id, 7, '0', STR_PAD_LEFT);
            $contact->ticket_number = $prefix . '/TKT/' . $idPart;
            $contact->save();
            return $contact;
        });
    }
}
