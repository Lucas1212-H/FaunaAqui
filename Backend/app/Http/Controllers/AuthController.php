<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * 📝 Registro de Novos Usuários (Colaborador / Administrador)
     */
    public function registrar(Request $request)
    {
        $dados = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        $dados['password'] = Hash::make($dados['password']);
        $dados['tipo_conta'] = 'Colaborador'; // Força a ser colaborador
        $dados['status'] = 'Pendente';       // Trava o acesso

        // Salva no banco de dados
        User::create($dados);

        // Retorna apenas o aviso de sucesso, sem emitir Token de login!
        return response()->json([
            'message' => 'Solicitação de cadastro enviada com sucesso! Aguarde a aprovação do administrador.'
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Credenciais inválidas. Verifique o e-mail e a senha.'
            ], 401);
        }

        // BARRAGEM DE SEGURANÇA: Se estiver pendente, não deixa logar!
        if ($user->status === 'Pendente') {
            return response()->json([
                'message' => 'Sua conta ainda está em análise pelo administrador. Um e-mail será enviado quando for aprovada.'
            ], 403); // Status 403 (Proibido)
        }

        $user->tokens()->delete();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'tipo_conta' => $user->tipo_conta
            ]
        ], 200);
    }
    /**
     * 
 * 👤 Atualizar o perfil do próprio usuário logado
 */
    public function atualizarPerfil(Request $request)
    {
        // Obtém o usuário autenticado via Token Sanctum
        $user = auth()->user(); 

        if (!$user) {
            return response()->json(['message' => 'Usuário não autenticado.'], 401);
        }

        $dados = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'string', 'min:6']
        ]);

        // Se o usuário digitou uma nova senha, encripta ela
        if (!empty($dados['password'])) {
            $dados['password'] = bcrypt($dados['password']);
        } else {
            unset($dados['password']); // Mantém a senha antiga se o campo estiver vazio
        }

        // Atualiza os dados no banco
        $user->update($dados);

        return response()->json([
            'message' => 'Perfil atualizado com sucesso!',
            'user' => $user
        ], 200);
    }

    public function deletarPerfil()
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['message' => 'Usuário não autenticado.'], 401);
        }

        // Revoga todos os tokens do Sanctum associados a este usuário (desloga de todos os locais)
        $user->tokens()->delete();

        // Deleta o registro do banco de dados
        $user->delete();

        return response()->json([
            'message' => 'Sua conta foi excluída permanentemente do sistema.'
        ], 200);
    }

    /**
     * Logout (Revogar Token)
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Sessão encerrada com sucesso!'
        ], 200);
    }
    public function excluirPropriaConta(Request $resquest)
    {
        $user = $resquest->user();
        if ($user->tipo_conta === 'Administrador') {
            $totalAdmins = \App\Models\User::where('tipo_conta', 'Administrador')->count();
            if ($totalAdmins <= 1) {
                return response()->json([
                    'message' => 'Não é possível excluir a última conta de Administrador.'
                ]);
            }
        }
        // Revoga todos os tokens do acesso associados a este usuário
        $user->tokens()->delete();
        // Exclui o usuário do banco de dados
        $user->delete();
        return response()->json([
            'message' => 'Sua conta foi excluída com sucesso.'
        ], 200);
    }

    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = \Illuminate\Support\Facades\Password::broker()->sendResetLink(
            $request->only('email')
        );

        if ($status === \Illuminate\Support\Facades\Password::RESET_LINK_SENT) {
            return response()->json(['message' => 'Um link de recuperação de senha foi enviado para o seu e-mail.'], 200);
        }

        return response()->json(['message' => 'Não foi possível enviar o link de recuperação. Verifique o e-mail informado.'], 400);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed',
        ]);

        $status = \Illuminate\Support\Facades\Password::broker()->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->save();
                
                // Opicional: se quiser deslogar de todas as sessões ao resetar a senha
                $user->tokens()->delete();
            }
        );

        if ($status === \Illuminate\Support\Facades\Password::PASSWORD_RESET) {
            return response()->json(['message' => 'Sua senha foi redefinida com sucesso!'], 200);
        }

        return response()->json(['message' => 'Token inválido ou expirado.'], 400);
    }
}