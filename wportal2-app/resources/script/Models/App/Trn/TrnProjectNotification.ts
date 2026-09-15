import { ListBase } from '@/script/Models/ListBase';
import {
  E_NOTIFICATION_TYPE,
  ENotificationType,
} from '@/script/Enum/Server/App/ENotificationType';

export interface IAppTrnProjectNotification {
  id: number;
  trnProjectId: number;
  notificationType: ENotificationType;
  notificationValue: string;
}

export const parseAppTrnProjectNotification = (
  payload: IAppTrnProjectNotification,
): IAppTrnProjectNotification => {
  return {
    id: payload.id ? Number(payload.id) : 0,
    trnProjectId: payload.trnProjectId ? Number(payload.trnProjectId) : 0,
    notificationType: payload.notificationType
      ? (Number(payload.notificationType) as ENotificationType)
      : E_NOTIFICATION_TYPE.INVALID,
    notificationValue: payload.notificationValue
      ? String(payload.notificationValue)
      : '',
  };
};

export class AppTrnProjectNotificationList extends ListBase<IAppTrnProjectNotification> {
  constructor(data: IAppTrnProjectNotification[] = []) {
    super(data);
  }

  static defaultInterface(): IAppTrnProjectNotification {
    return {
      id: 0,
      trnProjectId: 0,
      notificationType: E_NOTIFICATION_TYPE.INVALID,
      notificationValue: '',
    };
  }

  default = (): IAppTrnProjectNotification => {
    return AppTrnProjectNotificationList.defaultInterface();
  };

  getPrimary(value: IAppTrnProjectNotification): number {
    return value.id;
  }
}
