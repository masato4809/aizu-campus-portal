import { ListBase } from '@/script/Models/ListBase';
import {
  E_TARGET_TYPE,
  ETargetType,
} from '@/script/Enum/Server/App/GoodJob/ETargetType';
import { IAppTrnUser } from '@/script/Models/App/Trn/TrnUserList';
import { IAppTrnDivision } from '@/script/Models/App/Trn/TrnDivisionList';
import { IAppTrnProject } from '@/script/Models/App/Trn/TrnProjectList';

export interface IAppTrnGoodJob {
  id: number;

  trnUserId: number;

  fromTargetType: ETargetType;
  fromTrnUserId: number;
  fromTrnDivisionId: number;
  fromTrnProjectId: number;
  fromOtherLabel: string;
  toTargetType: ETargetType;
  toTrnUserId: number;
  toTrnDivisionId: number;
  toTrnProjectId: number;
  toOtherLabel: string;
  title: string;
  content: string;
  createdAt: string;

  fromTrnUser?: IAppTrnUser;
  fromTrnDivision?: IAppTrnDivision;
  fromTrnProject?: IAppTrnProject;
  toTrnUser?: IAppTrnUser;
  toTrnDivision?: IAppTrnDivision;
  toTrnProject?: IAppTrnProject;
}

export const parseAppTrnGoodJobPayload = (
  payload: IAppTrnGoodJob,
): IAppTrnGoodJob => {
  return {
    id: payload.id ? Number(payload.id) : 0,

    trnUserId: payload.trnUserId ? Number(payload.trnUserId) : 0,

    fromTargetType: payload.fromTargetType
      ? (Number(payload.fromTargetType) as ETargetType)
      : 0,
    fromTrnUserId: payload.fromTrnUserId ? Number(payload.fromTrnUserId) : 0,
    fromTrnDivisionId: payload.fromTrnDivisionId
      ? Number(payload.fromTrnDivisionId)
      : 0,
    fromTrnProjectId: payload.fromTrnProjectId
      ? Number(payload.fromTrnProjectId)
      : 0,
    fromOtherLabel: payload.fromOtherLabel
      ? String(payload.fromOtherLabel)
      : '',
    toTargetType: payload.toTargetType
      ? (Number(payload.toTargetType) as ETargetType)
      : 0,
    toTrnUserId: payload.toTrnUserId ? Number(payload.toTrnUserId) : 0,
    toTrnDivisionId: payload.toTrnDivisionId
      ? Number(payload.toTrnDivisionId)
      : 0,
    toTrnProjectId: payload.toTrnProjectId ? Number(payload.toTrnProjectId) : 0,
    toOtherLabel: payload.toOtherLabel ? String(payload.toOtherLabel) : '',
    title: payload.title ? String(payload.title) : '',
    content: payload.content ? String(payload.content) : '',
    createdAt: payload.createdAt ? String(payload.createdAt) : '',

    // FromTrnUser.
    fromTrnUser: payload.fromTrnUser ? payload.fromTrnUser : undefined,

    // FromTrnDivision.
    fromTrnDivision: payload.fromTrnDivision
      ? payload.fromTrnDivision
      : undefined,

    // FromTrnProject.
    fromTrnProject: payload.fromTrnProject ? payload.fromTrnProject : undefined,

    // ToTrnUser.
    toTrnUser: payload.toTrnUser ? payload.toTrnUser : undefined,

    // ToTrnDivision.
    toTrnDivision: payload.toTrnDivision ? payload.toTrnDivision : undefined,

    // ToTrnProject.
    toTrnProject: payload.toTrnProject ? payload.toTrnProject : undefined,
  };
};

export class AppTrnGoodJobList extends ListBase<IAppTrnGoodJob> {
  constructor(data: IAppTrnGoodJob[] = []) {
    super(data);
  }

  static defaultInterface(): IAppTrnGoodJob {
    return {
      id: 0,
      trnUserId: 0,
      fromTargetType: E_TARGET_TYPE.INVALID,
      fromTrnUserId: 0,
      fromTrnDivisionId: 0,
      fromTrnProjectId: 0,
      fromOtherLabel: '',
      toTargetType: E_TARGET_TYPE.INVALID,
      toTrnUserId: 0,
      toTrnDivisionId: 0,
      toTrnProjectId: 0,
      toOtherLabel: '',
      title: '',
      content: '',
      createdAt: '',
    };
  }

  default = (): IAppTrnGoodJob => {
    return AppTrnGoodJobList.defaultInterface();
  };

  getPrimary(value: IAppTrnGoodJob): number {
    return value.id;
  }
}
