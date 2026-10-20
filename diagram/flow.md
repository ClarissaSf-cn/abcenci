graph TD
    %% WARNA DAN GAYA (STYLING)
    classDef entity fill:#ffffff,stroke:#000000,stroke-width:2px;
    classDef relation fill:#ffffff,stroke:#000000,stroke-width:1px;
    classDef attribute fill:#ffffff,stroke:#000000,stroke-width:1px;

    %% 1. ENTITAS (KOTAK)
    U[Users]:::entity
    A[Attendances]:::entity
    E[Employees]:::entity
    L[Lembur]:::entity
    S[Stationeries]:::entity
    T[Transactions]:::entity
    P[Pengadaans]:::entity

    %% 2. RELASI (BELAH KETUPAT)
    R1{Melakukan}:::relation
    R3{Memiliki}:::relation
    R4{Mencatat}:::relation
    R5{Dipesan}:::relation

    %% 3. MENGHUBUNGKAN ENTITAS & RELASI (KARDINALITAS 1 TO M)
    U -- 1 --- R1 --- M --> A
    E -- 1 --- R3 --- M --> L
    S -- 1 --- R4 --- M --> T
    S -- 1 --- R5 --- M --> P

    %% 4. ATRIBUT USERS (OVAL)
    U1((<u>id</u>)):::attribute
    U2((name)):::attribute
    U3((role)):::attribute
    U --- U1 & U2 & U3

    %% 5. ATRIBUT ATTENDANCES (OVAL)
    A1((<u>id</u>)):::attribute
    A2((jam_masuk)):::attribute
    A3((status)):::attribute
    A --- A1 & A2 & A3

    %% 6. ATRIBUT EMPLOYEES (OVAL)
    E1((<u>id</u>)):::attribute
    E2((nik)):::attribute
    E3((nama)):::attribute
    E --- E1 & E2 & E3

    %% 7. ATRIBUT LEMBUR (OVAL)
    L1((<u>id</u>)):::attribute
    L2((jumlah_jam)):::attribute
    L --- L1 & L2

    %% 8. ATRIBUT STATIONERIES (OVAL)
    S1((<u>id</u>)):::attribute
    S2((nama_barang)):::attribute
    S3((total_stok)):::attribute
    S --- S1 & S2 & S3

    %% 9. ATRIBUT TRANSAKSI (OVAL)
    T1((<u>id</u>)):::attribute
    T2((type)):::attribute
    T3((jumlah)):::attribute
    T --- T1 & T2 & T3

    %% 10. ATRIBUT PENGADAAN (OVAL)
    P1((<u>id</u>)):::attribute
    P2((no_pr)):::attribute
    P --- P1 & P2
