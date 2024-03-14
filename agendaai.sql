-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 13/03/2024 às 23:22
-- Versão do servidor: 10.4.28-MariaDB
-- Versão do PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `agenda`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `agenda`
--

CREATE TABLE `agenda` (
  `id` int(11) NOT NULL COMMENT 'id da agenda',
  `id_empresa` int(11) NOT NULL COMMENT 'id da tabela de empresa',
  `nome` varchar(250) NOT NULL COMMENT 'nome',
  `codigo` varchar(7) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `agendamento`
--

CREATE TABLE `agendamento` (
  `id` int(11) NOT NULL COMMENT 'id do agendamento',
  `id_agenda` int(11) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `id_cliente` int(11) DEFAULT NULL,
  `id_funcionario` int(11) NOT NULL,
  `titulo` varchar(150) NOT NULL COMMENT 'titulo do agendamento',
  `dt_ini` datetime NOT NULL COMMENT 'data inicial do agendamento',
  `dt_fim` datetime NOT NULL COMMENT 'data final do agendamento',
  `cor` varchar(7) DEFAULT NULL COMMENT 'cor do agendamento',
  `total` decimal(10,2) NOT NULL,
  `status` int(11) NOT NULL COMMENT '1 -> finalizado 0 -> em andamento 99 -> Cancelado',
  `obs` varchar(400) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `agendamento_item`
--

CREATE TABLE `agendamento_item` (
  `id` int(11) NOT NULL,
  `id_agendamento` int(11) NOT NULL,
  `id_servico` int(11) NOT NULL,
  `qtd_item` int(11) NOT NULL,
  `tempo_item` time NOT NULL,
  `total_item` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `agenda_funcionario`
--

CREATE TABLE `agenda_funcionario` (
  `id_agenda` int(11) NOT NULL,
  `id_funcionario` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `agenda_usuario`
--

CREATE TABLE `agenda_usuario` (
  `id_agenda` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `cidade`
--

CREATE TABLE `cidade` (
  `id` int(11) NOT NULL,
  `nome` varchar(120) DEFAULT NULL,
  `uf` int(11) DEFAULT NULL,
  `ibge` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='Municipios das Unidades Federativas';

-- --------------------------------------------------------

--
-- Estrutura para tabela `cliente`
--

CREATE TABLE `cliente` (
  `id` int(11) NOT NULL,
  `nome` varchar(300) NOT NULL,
  `id_funcionario` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `empresa`
--

CREATE TABLE `empresa` (
  `id` int(11) NOT NULL,
  `nome` varchar(250) NOT NULL,
  `email` varchar(200) NOT NULL,
  `telefone` varchar(13) NOT NULL,
  `cnpj` varchar(14) NOT NULL,
  `razao` varchar(250) NOT NULL,
  `fantasia` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `endereco`
--

CREATE TABLE `endereco` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `cep` int(11) NOT NULL COMMENT 'cep',
  `id_cidade` int(11) NOT NULL COMMENT 'id tabela cidade',
  `id_estado` int(11) NOT NULL COMMENT 'id tabela estado',
  `bairro` varchar(150) NOT NULL COMMENT 'bairro',
  `rua` varchar(250) NOT NULL COMMENT 'endereco',
  `numero` int(11) NOT NULL,
  `complemento` varchar(250) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `estado`
--

CREATE TABLE `estado` (
  `id` int(11) NOT NULL,
  `nome` varchar(75) DEFAULT NULL,
  `uf` varchar(2) DEFAULT NULL,
  `ibge` int(11) DEFAULT NULL,
  `pais` int(11) DEFAULT NULL,
  `ddd` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='Unidades Federativas';

-- --------------------------------------------------------

--
-- Estrutura para tabela `funcionario`
--

CREATE TABLE `funcionario` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `nome` varchar(200) NOT NULL,
  `cpf_cnpj` varchar(14) NOT NULL,
  `email` varchar(200) NOT NULL,
  `telefone` varchar(13) NOT NULL,
  `hora_ini` time NOT NULL COMMENT 'horario incial de atendimento',
  `hora_fim` time NOT NULL COMMENT 'horario final de atendimento',
  `hora_almoco_ini` time NOT NULL,
  `hora_almoco_fim` time NOT NULL,
  `dias` varchar(27) NOT NULL COMMENT 'dom,seg,ter,qua,qui,sex,sab\r\n\r\nrepresentaria que o funcionario trabalharia todos os dias da semana'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `funcionario_grupo_funcionario`
--

CREATE TABLE `funcionario_grupo_funcionario` (
  `id_funcionario` int(11) NOT NULL,
  `id_grupo_funcionario` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `grupo_funcionario`
--

CREATE TABLE `grupo_funcionario` (
  `id` int(11) NOT NULL,
  `id_empresa` int(11) NOT NULL,
  `nome` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `grupo_servico`
--

CREATE TABLE `grupo_servico` (
  `id` int(11) NOT NULL,
  `id_empresa` int(11) NOT NULL,
  `nome` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `servico`
--

CREATE TABLE `servico` (
  `id` int(11) NOT NULL,
  `nome` varchar(250) NOT NULL,
  `valor` decimal(14,2) NOT NULL,
  `tempo` time NOT NULL,
  `id_empresa` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `servico_funcionario`
--

CREATE TABLE `servico_funcionario` (
  `id_funcionario` int(11) NOT NULL,
  `id_servico` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `servico_grupo_servico`
--

CREATE TABLE `servico_grupo_servico` (
  `id_grupo_servico` int(11) NOT NULL,
  `id_servico` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuario`
--

CREATE TABLE `usuario` (
  `id` int(11) NOT NULL COMMENT 'id do usuario',
  `nome` varchar(500) NOT NULL COMMENT 'nome do usuario',
  `cpf_cnpj` varchar(14) DEFAULT NULL,
  `telefone` varchar(11) DEFAULT NULL,
  `senha` varchar(150) DEFAULT NULL COMMENT 'senha do usuario',
  `email` varchar(200) DEFAULT NULL COMMENT 'email do usuario',
  `tipo_usuario` int(11) NOT NULL COMMENT '0 -> ADM |\r\n1 -> empresa |\r\n2 -> funcionario |\r\n3 -> usuario |\r\n4 -> cliente cadastrado |',
  `id_empresa` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='tabela de usuario';

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `agenda`
--
ALTER TABLE `agenda`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo` (`codigo`),
  ADD KEY `fk_agenda_empresa` (`id_empresa`);

--
-- Índices de tabela `agendamento`
--
ALTER TABLE `agendamento`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `getEventsbyFuncionario` (`id_agenda`,`id_funcionario`,`dt_ini`,`dt_fim`,`status`),
  ADD KEY `fk_agendamento_agenda` (`id_agenda`),
  ADD KEY `fk_agendamento_usuario` (`id_usuario`),
  ADD KEY `fk_agendamento_funcionario` (`id_funcionario`),
  ADD KEY `fk_agendamento_cliente` (`id_cliente`);

--
-- Índices de tabela `agendamento_item`
--
ALTER TABLE `agendamento_item`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `getItemByServico` (`id_agendamento`,`id_servico`) USING BTREE,
  ADD KEY `fk_agendamento_agendamento_item` (`id_agendamento`),
  ADD KEY `fk_servico_agendamento_item` (`id_servico`);

--
-- Índices de tabela `agenda_funcionario`
--
ALTER TABLE `agenda_funcionario`
  ADD PRIMARY KEY (`id_agenda`,`id_funcionario`),
  ADD KEY `fk_agenda_funcionario_funcionario` (`id_funcionario`);

--
-- Índices de tabela `agenda_usuario`
--
ALTER TABLE `agenda_usuario`
  ADD PRIMARY KEY (`id_agenda`,`id_usuario`),
  ADD KEY `fk_agenda_usuario_usuario` (`id_usuario`);

--
-- Índices de tabela `cidade`
--
ALTER TABLE `cidade`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_cidade_estado` (`uf`);

--
-- Índices de tabela `cliente`
--
ALTER TABLE `cliente`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_cliente_empresa` (`id_funcionario`);

--
-- Índices de tabela `empresa`
--
ALTER TABLE `empresa`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cnpj` (`cnpj`);

--
-- Índices de tabela `endereco`
--
ALTER TABLE `endereco`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_endereco_estado` (`id_estado`),
  ADD KEY `fk_endereco_cidade` (`id_cidade`),
  ADD KEY `fk_endereco_usuario` (`id_usuario`);

--
-- Índices de tabela `estado`
--
ALTER TABLE `estado`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `funcionario`
--
ALTER TABLE `funcionario`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_funcionario_usuario` (`id_usuario`);

--
-- Índices de tabela `funcionario_grupo_funcionario`
--
ALTER TABLE `funcionario_grupo_funcionario`
  ADD KEY `fk_funcionario_grupo_funcionario_funcionario` (`id_funcionario`),
  ADD KEY `fk_funcionario_grupo_funcionario_grupo_funcionario` (`id_grupo_funcionario`);

--
-- Índices de tabela `grupo_funcionario`
--
ALTER TABLE `grupo_funcionario`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `grupo_servico`
--
ALTER TABLE `grupo_servico`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `servico`
--
ALTER TABLE `servico`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_servico_empresa` (`id_empresa`) USING BTREE;

--
-- Índices de tabela `servico_funcionario`
--
ALTER TABLE `servico_funcionario`
  ADD PRIMARY KEY (`id_funcionario`,`id_servico`),
  ADD KEY `fk_servico_funcionario_servico` (`id_servico`);

--
-- Índices de tabela `servico_grupo_servico`
--
ALTER TABLE `servico_grupo_servico`
  ADD PRIMARY KEY (`id_grupo_servico`,`id_servico`),
  ADD KEY `fk_agenda_servico_servico` (`id_servico`);

--
-- Índices de tabela `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `cpf_cnpj` (`cpf_cnpj`);

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `agenda`
--
ALTER TABLE `agenda`
  ADD CONSTRAINT `fk_agenda_empresa` FOREIGN KEY (`id_empresa`) REFERENCES `empresa` (`id`);

--
-- Restrições para tabelas `agendamento`
--
ALTER TABLE `agendamento`
  ADD CONSTRAINT `fk_agendamento_agenda` FOREIGN KEY (`id_agenda`) REFERENCES `agenda` (`id`),
  ADD CONSTRAINT `fk_agendamento_cliente` FOREIGN KEY (`id_cliente`) REFERENCES `cliente` (`id`),
  ADD CONSTRAINT `fk_agendamento_funcionario` FOREIGN KEY (`id_funcionario`) REFERENCES `funcionario` (`id`),
  ADD CONSTRAINT `fk_agendamento_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id`);

--
-- Restrições para tabelas `agendamento_item`
--
ALTER TABLE `agendamento_item`
  ADD CONSTRAINT `fk_agendamento_agendamento_item` FOREIGN KEY (`id_agendamento`) REFERENCES `agendamento` (`id`),
  ADD CONSTRAINT `fk_servico_agendamento_item` FOREIGN KEY (`id_servico`) REFERENCES `servico` (`id`);

--
-- Restrições para tabelas `agenda_funcionario`
--
ALTER TABLE `agenda_funcionario`
  ADD CONSTRAINT `fk_agenda_funcionario_agenda` FOREIGN KEY (`id_agenda`) REFERENCES `agenda` (`id`),
  ADD CONSTRAINT `fk_agenda_funcionario_funcionario` FOREIGN KEY (`id_funcionario`) REFERENCES `funcionario` (`id`);

--
-- Restrições para tabelas `agenda_usuario`
--
ALTER TABLE `agenda_usuario`
  ADD CONSTRAINT `fk_agenda_usuario_agenda` FOREIGN KEY (`id_agenda`) REFERENCES `agenda` (`id`),
  ADD CONSTRAINT `fk_agenda_usuario_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id`);

--
-- Restrições para tabelas `cidade`
--
ALTER TABLE `cidade`
  ADD CONSTRAINT `fk_cidade_estado` FOREIGN KEY (`uf`) REFERENCES `estado` (`id`);

--
-- Restrições para tabelas `cliente`
--
ALTER TABLE `cliente`
  ADD CONSTRAINT `fk_cliente_empresa` FOREIGN KEY (`id_funcionario`) REFERENCES `empresa` (`id`);

--
-- Restrições para tabelas `endereco`
--
ALTER TABLE `endereco`
  ADD CONSTRAINT `fk_endereco_cidade` FOREIGN KEY (`id_cidade`) REFERENCES `cidade` (`id`),
  ADD CONSTRAINT `fk_endereco_estado` FOREIGN KEY (`id_estado`) REFERENCES `estado` (`id`),
  ADD CONSTRAINT `fk_endereco_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id`);

--
-- Restrições para tabelas `funcionario`
--
ALTER TABLE `funcionario`
  ADD CONSTRAINT `fk_funcionario_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id`);

--
-- Restrições para tabelas `funcionario_grupo_funcionario`
--
ALTER TABLE `funcionario_grupo_funcionario`
  ADD CONSTRAINT `fk_funcionario_grupo_funcionario_funcionario` FOREIGN KEY (`id_funcionario`) REFERENCES `funcionario` (`id`),
  ADD CONSTRAINT `fk_funcionario_grupo_funcionario_grupo_funcionario` FOREIGN KEY (`id_grupo_funcionario`) REFERENCES `grupo_funcionario` (`id`);

--
-- Restrições para tabelas `servico`
--
ALTER TABLE `servico`
  ADD CONSTRAINT `fk_servico_empresa` FOREIGN KEY (`id_empresa`) REFERENCES `empresa` (`id`);

--
-- Restrições para tabelas `servico_funcionario`
--
ALTER TABLE `servico_funcionario`
  ADD CONSTRAINT `fk_servico_funcionario_funcionario` FOREIGN KEY (`id_funcionario`) REFERENCES `funcionario` (`id`),
  ADD CONSTRAINT `fk_servico_funcionario_servico` FOREIGN KEY (`id_servico`) REFERENCES `servico` (`id`);

--
-- Restrições para tabelas `servico_grupo_servico`
--
ALTER TABLE `servico_grupo_servico`
  ADD CONSTRAINT `fk_grupo_servico_servico` FOREIGN KEY (`id_servico`) REFERENCES `servico` (`id`),
  ADD CONSTRAINT `fk_servico_grupo_servico` FOREIGN KEY (`id_grupo_servico`) REFERENCES `grupo_servico` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
