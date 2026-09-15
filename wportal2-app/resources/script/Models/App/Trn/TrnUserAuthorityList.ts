import { ListBase } from '@/script/Models/ListBase';
import {
  E_USER_AUTHORITY,
  EUserAuthority,
} from '@/script/Enum/Server/App/EUserAuthority';

export interface IAppTrnUserAuthority {
  id: number;
  trnUserId: number;
  eUserAuthority: EUserAuthority;
}

export const parseAppTrnUserAuthorityPayload = (
  payload: IAppTrnUserAuthority,
): IAppTrnUserAuthority => {
  return {
    id: payload.id ? Number(payload.id) : 0,
    trnUserId: payload.trnUserId ? Number(payload.trnUserId) : 0,
    eUserAuthority: payload.eUserAuthority
      ? (Number(payload.eUserAuthority) as EUserAuthority)
      : E_USER_AUTHORITY.INVALID,
  };
};

export class AppTrnUserAuthorityList extends ListBase<IAppTrnUserAuthority> {
  constructor(data: IAppTrnUserAuthority[] = []) {
    super(data);
  }

  static defaultInterface(): IAppTrnUserAuthority {
    return {
      id: 0,
      trnUserId: 0,
      eUserAuthority: E_USER_AUTHORITY.INVALID,
    };
  }

  default = (): IAppTrnUserAuthority => {
    return AppTrnUserAuthorityList.defaultInterface();
  };

  getPrimary(value: IAppTrnUserAuthority): number {
    return value.id;
  }

  /**
   * 特権所有かどうか.
   */
  public hasRootPrivilege(): boolean {
    return !!this.list().find(
      v => v.eUserAuthority === E_USER_AUTHORITY.ROOT_PRIVILEGE,
    );
  }

  /**
   * 指定権限を所有しているかどうか.
   */
  public hasAuthority(authority: EUserAuthority[]): boolean {
    if (this.hasRootPrivilege()) {
      return true;
    }

    return authority.some(v =>
      this.list()
        .map(v => v.eUserAuthority)
        .includes(v),
    );
  }
}
