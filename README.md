# 🛡️ Projeto Blue Team vs. Red Team ⚔️

Este repositório contém os documentos e entregáveis de um projeto de cibersegurança que envolveu atividades de **Blue Team**, **Red Team** e **Purple Team**. O projeto simulou um cenário real onde as equipes configuraram ambientes de rede seguros, realizaram testes de invasão e, por fim, propuseram melhorias.

---

## 📂 Estrutura do Projeto

A estrutura de arquivos deste repositório está organizada da seguinte forma:
├── Blue Team
│   ├── architecture.png            # Diagrama da arquitetura (Excalidraw)
│   ├── Blue Team - Final Report.pdf # Relatório final do Blue Team
│   ├── main.tex                    # Código fonte LaTeX do relatório
│   └── WebApp                      # Aplicação web desenvolvida
├── Red Team
│   ├── G2
│   │   ├── Pentest_Report_G2.pdf   # Relatório de Pentest (Grupo 2)
│   │   └── main.tex                # Código fonte LaTeX (G2)
│   ├── G4
│   │   ├── Pentest_Report_G4.pdf   # Relatório de Pentest (Grupo 4)
│   │   └── main.tex                # Código fonte LaTeX (G4)
│   └── G15
│       ├── Pentest_Report_G15.pdf  # Relatório de Pentest (Grupo 15)
│       └── main.tex                # Código fonte LaTeX (G15)
└── Purple Team
├── PurpleTeam_Report.pdf       # Relatório com propostas de melhoria
└── main.tex                    # Código fonte LaTeX (Purple Team)

---

## 🎯 Dinâmica do Projeto

O projeto foi dividido em três fases principais:

### 1. Fase Blue Team (Defesa) 🛡️
Cada grupo configurou um ambiente de rede seguro com três máquinas:
- **Firewall**: Responsável pela segurança da rede e filtragem de tráfego.
- **DMZ**: Hospedava serviços públicos, como servidores web e de e-mail.
- **Rede Interna**: Continha serviços sensíveis, como bancos de dados e compartilhamento de arquivos.

### 2. Fase Red Team (Ataque) ⚔️
Após o período de configuração, cada grupo realizou testes de invasão (*pentests*) nos ambientes dos outros grupos para identificar e explorar vulnerabilidades.

### 3. Fase Purple Team (Análise) 🤝
Foi produzido um relatório colaborativo, unindo as defesas do Blue Team e os achados do Red Team para propor melhorias de segurança para todos os ambientes.

---

## 📋 Requisitos do Blue Team

O Blue Team foi encarregado de configurar um ambiente seguro com os seguintes serviços obrigatórios:

- **Servidor de E-mail**: Configurado para comunicação segura.
- **Aplicação Web**:
  - Mínimo de 3 páginas.
  - Conectada a um banco de dados.
- **Servidor de Arquivos**: Suporte a protocolos como FTP ou SMB.
- **Serviço de Backup**: Para garantir a recuperação de dados.
- **Conexão Interativa**: Acesso remoto seguro (ex: RDP, SSH).
- **Autenticação de Usuários**: Implementação de PAM, MFA ou mecanismos similares.
- **Proteção contra DoS**: Mitigação de ataques de negação de serviço.
- **Proteção contra Vazamento de Dados (DLP)**: Para prevenir a exfiltração de dados não autorizada.
- **Serviços Adicionais de Segurança**: Conforme a necessidade para proteger o ambiente.

---

## 💻 Nossa Implementação

Nossa equipe configurou os seguintes serviços, distribuídos entre as três máquinas do ambiente:

### 🔥 Firewall
- **PfSense**: Solução utilizada como firewall de borda.
- **Snort**: Implementado como IDS/IPS para detecção e prevenção de intrusões.

### 🌐 DMZ (Zona Desmilitarizada)
- **Mailcow**: Servidor de e-mail utilizando **Dovecot** e **Postfix**.
- **Apache + WebApp**: Servidor web para hospedar a aplicação customizada.
- **OpenSSH**: Configurado com autenticação por chaves pública/privada e MFA.

### 🔒 Rede Interna
- **MySQL**: Banco de dados para a aplicação web.
- **SAMBA**: Compartilhamento de arquivos baseado no protocolo SMB.
- **Restic**: Solução de backup para recuperação de dados.
- **Wazuh**: Ferramenta SIEM e XDR para monitoramento de segurança contínuo.
- **YARA**: Regras para Prevenção de Perda de Dados (DLP).
- **LLDAP**: Servidor LDAP leve para centralização de autenticação.
- **HashiCorp Vault**: Gerenciamento de Acesso Privilegiado (PAM) e segredos.
- **OpenSSH**: Configurado com autenticação por chaves pública/privada e MFA.
