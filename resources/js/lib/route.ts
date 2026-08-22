import loginRoutes from '@/routes/login';
import { logout, home, dashboard } from '@/routes/index';
import profile from '@/routes/profile';
import security from '@/routes/security';
import userPassword from '@/routes/user-password';
import master from '@/routes/master';
import penetapan from '@/routes/penetapan';
import pelaksanaan from '@/routes/pelaksanaan';
import evaluasi from '@/routes/evaluasi';
import pengendalian from '@/routes/pengendalian';
import peningkatan from '@/routes/peningkatan';

type Params = Record<string, unknown> | number;

function extractFirstParam(params?: Params): number | undefined {
    if (!params) return undefined;
    if (typeof params === 'number') return params;
    const values = Object.values(params);
    return values.length > 0 ? Number(values[0]) : undefined;
}

const routes: Record<string, (params?: Params) => string> = {
    'dashboard': () => dashboard().url,
    'home': () => home().url,
    'login.store': () => loginRoutes.store().url,
    'logout': () => logout().url,
    'profile.edit': () => profile.edit().url,
    'profile.update': () => profile.update().url,
    'profile.destroy': () => profile.destroy().url,
    'user-password.update': () => userPassword.update().url,
    'security.edit': () => security.edit().url,
    'master.users.index': () => master.users.index().url,
    'master.users.store': () => master.users.store().url,
    'master.users.update': (p) => master.users.update(extractFirstParam(p)!).url,
    'master.users.destroy': (p) => master.users.destroy(extractFirstParam(p)!).url,
    'master.unit-kerja.index': () => master.unitKerja.index().url,
    'master.unit-kerja.store': () => master.unitKerja.store().url,
    'master.unit-kerja.update': (p) => master.unitKerja.update(extractFirstParam(p)!).url,
    'master.unit-kerja.destroy': (p) => master.unitKerja.destroy(extractFirstParam(p)!).url,
    'master.kategori-standar.index': () => master.kategoriStandar.index().url,
    'master.kategori-standar.store': () => master.kategoriStandar.store().url,
    'master.kategori-standar.update': (p) => master.kategoriStandar.update(extractFirstParam(p)!).url,
    'master.kategori-standar.destroy': (p) => master.kategoriStandar.destroy(extractFirstParam(p)!).url,
    'penetapan.periode.index': () => penetapan.periode.index().url,
    'penetapan.periode.store': () => penetapan.periode.store().url,
    'penetapan.periode.update': (p) => penetapan.periode.update(extractFirstParam(p)!).url,
    'penetapan.periode.destroy': (p) => penetapan.periode.destroy(extractFirstParam(p)!).url,
    'penetapan.standar.index': () => penetapan.standar.index().url,
    'penetapan.standar.store': () => penetapan.standar.store().url,
    'penetapan.standar.update': (p) => penetapan.standar.update(extractFirstParam(p)!).url,
    'penetapan.standar.destroy': (p) => penetapan.standar.destroy(extractFirstParam(p)!).url,
    'penetapan.standar.import.index': () => penetapan.standar.import.index().url,
    'penetapan.standar.import.store': () => penetapan.standar.import.store().url,
    'penetapan.indikator.index': () => penetapan.indikator.index().url,
    'penetapan.indikator.store': () => penetapan.indikator.store().url,
    'penetapan.indikator.update': (p) => penetapan.indikator.update(extractFirstParam(p)!).url,
    'penetapan.indikator.destroy': (p) => penetapan.indikator.destroy(extractFirstParam(p)!).url,
    'penetapan.distribusi-target.index': () => penetapan.distribusiTarget.index().url,
    'penetapan.distribusi-target.store': () => penetapan.distribusiTarget.store().url,
    'pelaksanaan.evaluasi-diri.index': () => pelaksanaan.evaluasiDiri.index().url,
    'pelaksanaan.evaluasi-diri.store': (p) => pelaksanaan.evaluasiDiri.store(extractFirstParam(p)!).url,
    'evaluasi.jadwal-audit.index': () => evaluasi.jadwalAudit.index().url,
    'evaluasi.kka.show': (p) => evaluasi.kka.show(extractFirstParam(p)!).url,
    'evaluasi.kka.store': (p) => evaluasi.kka.store(extractFirstParam(p)!).url,
    'pengendalian.isi-rtl.index': () => pengendalian.isiRtl.index().url,
    'pengendalian.isi-rtl.store': (p) => pengendalian.isiRtl.store(extractFirstParam(p)!).url,
    'peningkatan.risalah.index': () => peningkatan.risalah.index().url,
    'peningkatan.risalah.store': () => peningkatan.risalah.store().url,
    'peningkatan.risalah.update': (p) => peningkatan.risalah.update(extractFirstParam(p)!).url,
    'peningkatan.risalah.destroy': (p) => peningkatan.risalah.destroy(extractFirstParam(p)!).url,
};

function resolveRoute(name: string, params?: Params): string {
    const routeFn = routes[name];
    if (!routeFn) {
        console.warn(`Route "${name}" not found`);
        return '/';
    }
    return routeFn(params);
}

resolveRoute.current = (name: string): boolean => {
    const routeFn = routes[name];
    if (!routeFn) return false;
    return window.location.pathname.startsWith(routeFn());
};

export { resolveRoute as route };
