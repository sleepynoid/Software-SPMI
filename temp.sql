create table Standard {
    id_standard int primary key,
    id_indikator int,
    foreign key (id_indikator)
    references indikator(id_indikator)
    type_standard ENUM('1', '2') NOT NULL
}

create table indikator {
    id_indikator int primary key,
    id_standard int,
}

create table 