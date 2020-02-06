<?php

namespace App\Services\PostfixPolicyInstallation;

use App\Models\ClientSenderAccess as ClientSenderAccessModel;

class ClientSenderAccessHandler extends AbstractHandler
{
    const POLICY_DENIED = 'Access denied by bp-policy';
    const POLICY_GRANTED = 'Access granted by bp-policy';

    public function install()
    {
        $this->task->update(['message' => 'Blacklist/Whitelist wird aktualisiert...']);

        $this->insert($this->getClientSenderAccessRows());
    }

    protected function table()
    {
        return 'client_sender_access';
    }

    protected function getClientSenderAccessRows()
    {
        $clientSenderAccess = ClientSenderAccessModel::where('active', '=', 1)->get();

        return $clientSenderAccess->map(function ($row) {
            return collect($row->toArray())
                ->only(['client_type', 'client_payload', 'sender_type', 'sender_payload', 'action'])
                ->pipe(function($row) {
                    $row->put('action', strtolower($row->get('action')));

                    switch ($row->get('action')) {
                        case 'reject':
                            $row->put('action', $row->get('action') . ' ' . self::POLICY_DENIED);
                            break;
                        case 'ok':
                            $row->put('action', $row->get('action') . ' ' . self::POLICY_GRANTED);
                            break;
                    }

                    return $row;
                })
                ->all();
        })->toArray();
    }
}