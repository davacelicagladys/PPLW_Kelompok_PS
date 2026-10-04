--
-- PostgreSQL database dump
--

\restrict Qhain9pQiKj2QuFTEpKzE1vr0nKvUd8N4Vkm2YKEderUHSqBK1Jqm4CGYGd4jeT

-- Dumped from database version 17.11 (Debian 17.11-0+deb13u1)
-- Dumped by pg_dump version 17.11 (Debian 17.11-0+deb13u1)

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET transaction_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: kategori_ruang; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.kategori_ruang (
    id bigint NOT NULL,
    nama character varying(255) NOT NULL,
    deskripsi character varying(255) NOT NULL
);


ALTER TABLE public.kategori_ruang OWNER TO postgres;

--
-- Name: kategori_ruang_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

ALTER TABLE public.kategori_ruang ALTER COLUMN id ADD GENERATED ALWAYS AS IDENTITY (
    SEQUENCE NAME public.kategori_ruang_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1
);


--
-- Name: kategori_ruang_unit; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.kategori_ruang_unit (
    kategori_ruang_id bigint NOT NULL,
    unit_id bigint NOT NULL
);


ALTER TABLE public.kategori_ruang_unit OWNER TO postgres;

--
-- Name: kategori_unit; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.kategori_unit (
    id bigint NOT NULL,
    nama character varying(255) NOT NULL,
    deskripsi character varying(255) NOT NULL
);


ALTER TABLE public.kategori_unit OWNER TO postgres;

--
-- Name: kategori_unit_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

ALTER TABLE public.kategori_unit ALTER COLUMN id ADD GENERATED ALWAYS AS IDENTITY (
    SEQUENCE NAME public.kategori_unit_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1
);


--
-- Name: request_booking; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.request_booking (
    id bigint NOT NULL,
    nama_depan character varying(255) NOT NULL,
    nama_belakang character varying(255) NOT NULL,
    no_wa character varying(14) NOT NULL,
    ruang_id bigint,
    pesan_untuk_tanggal date NOT NULL,
    jam_mulai time without time zone NOT NULL,
    durasi interval NOT NULL,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL,
    approval_status character varying(15),
    bukti_pembayaran character varying(255)
);


ALTER TABLE public.request_booking OWNER TO postgres;

--
-- Name: request_booking_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

ALTER TABLE public.request_booking ALTER COLUMN id ADD GENERATED ALWAYS AS IDENTITY (
    SEQUENCE NAME public.request_booking_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1
);


--
-- Name: ruang; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.ruang (
    id bigint NOT NULL,
    nama character varying(255) NOT NULL,
    jumlah_unit integer NOT NULL,
    kategori_ruang bigint,
    tarif_per_jam numeric,
    is_active boolean DEFAULT true NOT NULL,
    deskripsi character varying(255) NOT NULL
);


ALTER TABLE public.ruang OWNER TO postgres;

--
-- Name: ruang_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

ALTER TABLE public.ruang ALTER COLUMN id ADD GENERATED ALWAYS AS IDENTITY (
    SEQUENCE NAME public.ruang_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1
);


--
-- Name: unit; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.unit (
    id bigint NOT NULL,
    nama character varying(255) NOT NULL,
    jumlah_unit integer NOT NULL,
    kategori_unit bigint,
    is_active boolean DEFAULT true NOT NULL,
    deskripsi character varying(255) NOT NULL
);


ALTER TABLE public.unit OWNER TO postgres;

--
-- Name: unit_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

ALTER TABLE public.unit ALTER COLUMN id ADD GENERATED ALWAYS AS IDENTITY (
    SEQUENCE NAME public.unit_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1
);


--
-- Name: kategori_ruang kategori_ruang_nama_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.kategori_ruang
    ADD CONSTRAINT kategori_ruang_nama_key UNIQUE (nama);


--
-- Name: kategori_ruang kategori_ruang_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.kategori_ruang
    ADD CONSTRAINT kategori_ruang_pkey PRIMARY KEY (id);


--
-- Name: kategori_ruang_unit kategori_ruang_unit_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.kategori_ruang_unit
    ADD CONSTRAINT kategori_ruang_unit_pkey PRIMARY KEY (kategori_ruang_id, unit_id);


--
-- Name: kategori_unit kategori_unit_nama_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.kategori_unit
    ADD CONSTRAINT kategori_unit_nama_key UNIQUE (nama);


--
-- Name: kategori_unit kategori_unit_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.kategori_unit
    ADD CONSTRAINT kategori_unit_pkey PRIMARY KEY (id);


--
-- Name: request_booking request_booking_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.request_booking
    ADD CONSTRAINT request_booking_pkey PRIMARY KEY (id);


--
-- Name: ruang ruang_nama_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.ruang
    ADD CONSTRAINT ruang_nama_key UNIQUE (nama);


--
-- Name: ruang ruang_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.ruang
    ADD CONSTRAINT ruang_pkey PRIMARY KEY (id);


--
-- Name: unit unit_nama_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.unit
    ADD CONSTRAINT unit_nama_key UNIQUE (nama);


--
-- Name: unit unit_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.unit
    ADD CONSTRAINT unit_pkey PRIMARY KEY (id);


--
-- Name: ruang fk_kategori_ruang; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.ruang
    ADD CONSTRAINT fk_kategori_ruang FOREIGN KEY (kategori_ruang) REFERENCES public.kategori_ruang(id);


--
-- Name: kategori_ruang_unit fk_kategori_ruang_id; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.kategori_ruang_unit
    ADD CONSTRAINT fk_kategori_ruang_id FOREIGN KEY (kategori_ruang_id) REFERENCES public.kategori_ruang(id) ON DELETE CASCADE;


--
-- Name: unit fk_kategori_unit; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.unit
    ADD CONSTRAINT fk_kategori_unit FOREIGN KEY (kategori_unit) REFERENCES public.kategori_unit(id);


--
-- Name: request_booking fk_ruang_id; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.request_booking
    ADD CONSTRAINT fk_ruang_id FOREIGN KEY (ruang_id) REFERENCES public.ruang(id);


--
-- Name: kategori_ruang_unit fk_unit_id; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.kategori_ruang_unit
    ADD CONSTRAINT fk_unit_id FOREIGN KEY (unit_id) REFERENCES public.unit(id) ON DELETE CASCADE;


--
-- Name: TABLE kategori_ruang; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON TABLE public.kategori_ruang TO framework_user;


--
-- Name: SEQUENCE kategori_ruang_id_seq; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON SEQUENCE public.kategori_ruang_id_seq TO framework_user;


--
-- Name: TABLE kategori_ruang_unit; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON TABLE public.kategori_ruang_unit TO framework_user;


--
-- Name: TABLE kategori_unit; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON TABLE public.kategori_unit TO framework_user;


--
-- Name: SEQUENCE kategori_unit_id_seq; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON SEQUENCE public.kategori_unit_id_seq TO framework_user;


--
-- Name: TABLE request_booking; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON TABLE public.request_booking TO framework_user;


--
-- Name: SEQUENCE request_booking_id_seq; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON SEQUENCE public.request_booking_id_seq TO framework_user;


--
-- Name: TABLE ruang; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON TABLE public.ruang TO framework_user;


--
-- Name: SEQUENCE ruang_id_seq; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON SEQUENCE public.ruang_id_seq TO framework_user;


--
-- Name: TABLE unit; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON TABLE public.unit TO framework_user;


--
-- Name: SEQUENCE unit_id_seq; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON SEQUENCE public.unit_id_seq TO framework_user;


--
-- PostgreSQL database dump complete
--

\unrestrict Qhain9pQiKj2QuFTEpKzE1vr0nKvUd8N4Vkm2YKEderUHSqBK1Jqm4CGYGd4jeT

